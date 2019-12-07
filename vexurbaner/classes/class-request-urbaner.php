<?php

header('Content-Type: text/html; charset=UTF-8');

/**
 * 2007-2019 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
require_once dirname(__FILE__) . '/class-curl.php';
class VexUrbanerRequest
{
    public $orders;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public static function getPriceUrbaner($url, $data)
    {
        $login = Configuration::get(VexUrbaner::CONFIG_API_KEY);
        $curl = new VexUrbanerCurl();
        $curl->setHeader('authorization', 'token ' . $login);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setOpt(64, false);
        try {
            $result = $curl->post($url, json_encode($data));

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function checkCredentials()
    {
        $login = Configuration::get(VexUrbaner::CONFIG_API_KEY);
        if (empty($login)) {
            return false;
        }

        return array($login);
    }

    public function getDataResource($id, $idws)
    {
        // $order = $this->getOrder($id);
        $cart = new Cart($id);
        $store = Vex_Request_Sql::getStoreWsId($idws);
        $data = VexUrbanerRequest::getAddress($id);
        $order = Vex_Request_Sql::getOrderWs($id, $idws);
        $resurce = array(
            'type' => $order['type'],
            'destinations' => array(
                array(
                    'contact_person' => $store['persone'],
                    'phone' => $store['phone'],
                    'address' => $store['address'],
                    'latlon' => $store['lat'] . ' , ' . $store['lnt'],
                    'special_instructions' => $store['address_2'],
                ),
                array(
                    'contact_person' => $data['contact'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'latlon' => $order['lanLot'],
                ),
            ),
            'payment' => array(
                'backend' => Configuration::get('type_price'),
            ),
            'description' => $cart->getProducts()[0]['description_short'],
            'vehicle_id' => '2',
        );
        if ($order['type'] == 1) {
            $resurce['programmed_date'] = $order['date'];
        }

        return $resurce;
    }

    public static function getAddress($id)
    {
        $cart = new Cart($id);
        foreach ($cart->getAddressCollection() as $address) {
            $city = $address->city;
            $country = $address->country;
            $address1 = $address->address1;
            $address2 = $address->address2;
            $phone = $address->phone;
            $name = $address->firstname;
            $lastname = $address->lastname;
        }
        if (empty($address1)) {
            return false;
        }

        return array(
            'address' => $address1 . ', ' . $address2 . $city . ',   ' . $country,
            'contact' => $name . ' ' . $lastname,
            'phone' => $phone,
        );
    }

    public static function getPriceType($url)
    {
        $login = self::checkCredentials();
        if (!$login) {
            return false;
        }
        $curl = new VexUrbanerCurl();
        $curl->setHeader('authorization', 'token ' . $login[0]);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setOpt(64, false);
        try {
            $result = $curl->get($url);
            $result = json_decode($result->response, true);

            if (is_array($result) && count($result) > 0) {
                if (!empty($result['results'])) {
                    return $result['results'];
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function coordinates($id)
    {
        $address = self::getAddress($id);
        $apiMpas = Configuration::get(VexUrbaner::CONFIG_KEY_GOOGLE_MAPS);
        if (!$address) {
            return false;
        }
        if (!empty($apiMpas)) {
            $geo = Tools::file_get_contents(
                'https://maps.googleapis.com/maps/api/geocode/json?key=' .
                    $apiMpas .
                    '&address=' .
                    urlencode($address['address']) .
                    '&sensor=false'
            );
            $geo = json_decode($geo, true);
            if ($geo['status'] == 'OK') {
                // Obtener los valores
                $latitud = $geo['results'][0]['geometry']['location']['lat'];
                $longitud = $geo['results'][0]['geometry']['location']['lng'];
            } else {
                Logger::addLog(
                    '[' . 'Urbaner' . '][' . time() . '] ' .
                        $geo['error_message']
                );

                return false;
            }
        } else {
            return false;
        }

        return  array(
            'lat' => $latitud,
            'lnt' => $longitud,
        );
    }

    public function createOrder($id, $idWs)
    {
        $query = array();
        $login = Configuration::get(VexUrbaner::CONFIG_API_KEY);
        $curl = new VexUrbanerCurl();
        $curl->setHeader('authorization', 'token ' . $login);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setOpt(64, false);
        $data = $this->getDataResource($id, $idWs);
        $query = array();
        try {
            $result = $curl->post($this->module->module->getUrl() . 'cli/order/', json_encode($data));

            if (empty($result->error)) {
                // emviar email al correo de la tienda
                $rest = json_decode($result->response);
                $query = array(
                    'response' => 1,
                    'urlTracking' => $rest->tracking,
                    'id_traking' => $rest->id,
                    'status' => $rest->status,
                );
                return $this->sendEMail($id, $idWs);
            } else {
                $query = array(
                    'response' => -1,
                    'status' => $this->module->l('Error'),
                );
            }
        } catch (Exception $e) {
            Logger::addLog('Urbaner: Install module ' . $e->getMessage());
        }

        Db::getInstance()
            ->update('vex_urbaner_orders', $query, "id_vex_urbaner = $id");

        return $result;
    }

    public function sendEMail($id, $idWsl)
    {
        $store = Vex_Request_Sql::getStoreWsId($idWsl);
        $productDescription = '';
        $ordes_products = array();
        $message = 'Se ha generado una orden de envío, de Urbaner, por los siguientes productos: ';
        $cart = new Cart($id);
        foreach ($cart->getProducts() as $product) {
            $prod = Vex_Request_Sql::getProduct($product['id_product']);
            if ($prod['id_ws_seller'] == $idWsl) {
                $val = array(
                    'count' => $product['cart_quantity'],
                    'name'  => $product['name'],
                    'price' => $product['total_wt']
                );

                array_push($ordes_products, $val);
            }
        }

        if (is_array($ordes_products && count($ordes_products) > 0)) {
            foreach ($ordes_products as $order) {
                $message .= $order['count'] . ' ' . $order['name'] . ' por un total de $' . $order['price'] . ', ';
            }
        }
        return $message;

        $vars = array(
            '{firstname}' => $store['persone'],
            '{lastname}' => $store['name_ws'],
            '{order_name}' => 'Urbaner',
            '{message}' => 'prueba',
        );
        Mail::Send(
            (int) Context::getContext()->language->id,
            'order_merchant_comment',
            Context::getContext()->getTranslator()->trans(
                'Se ha creado una orden a Urbaner',
                array(),
                'Emails.Subject',
                Context::getContext()->language->locale
            ),
            $vars,
            'ludwins36@gmail.com',
            $store['persone'] . ' ' . $store['name_ws'],
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            null
        );
    }

    public static function getHorarysFront($time = 0)
    {
        $horarys = Vex_Request_Sql::getHoraryActive();
        if (sizeof($horarys)) {
            $day = strtotime('now');
            $asing = array();
            $today = false;

            foreach ($horarys as $b) {
                $date1 = new DateTime($b['start']);
                $date2 = new DateTime($b['end']);
                $interval = $date1->diff($date2);
                $start = $date1->getTimestamp() + $time;
                $end = $date2->getTimestamp() + $time - 3600;

                if ($b['id'] == date('N')) {
                    for ($i = 1; $i <= $interval->h; ++$i) {
                        $day += 3600;

                        if ($day > $start && $day < $end) {
                            $today = true;

                            $dateDays = array();
                            array_push($dateDays, date('Y-m-d', $day), date('H:i', $day), date('H:i', $day + 3600));

                            array_push($asing, $dateDays);
                        }
                    }
                } else {
                    $hoy = date('N');
                    if ($b['id'] > $hoy) {
                        $control = $b['id'] - $hoy;
                    } else {
                        $control = $hoy - $b['id'];
                        $control = 7 - $control;
                    }

                    $da = 86400 * $control;
                    $start += $da;
                    $end += $da;
                    $d = $start;
                    for ($i = 1; $i <= $interval->h; ++$i) {
                        $d += 3600;
                        if ($d > $start && $d < $end) {
                            $dateDays = array();
                            array_push(
                                $dateDays,
                                date('Y-m-d', $d),
                                date('H:i', $d),
                                date('H:i', $d + 3600)
                            );
                            array_push($asing, $dateDays);
                        }
                    }
                }
            }
            sort($asing);
            $success = array(
                'horarys' => $asing,
                'today' => $today,
            );

            return $success;
        }

        return false;
    }
}
