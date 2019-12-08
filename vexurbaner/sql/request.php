<?php

class Vex_Request_Sql
{
    public static function getOrders($type)
    {
        $query = '';
        if ($type) {
            $query = 'SELECT id_vex_urbaner,
            referency,
            response,
            date,
            price,
            status,
            id_traking,
            address,
            date_creation FROM ' . _DB_PREFIX_ . 'vex_urbaner_orders Where  response = 1 ORDER BY date_creation DESC';
        } else {
            $query = 'SELECT id_vex_urbaner,
            referency,
            response,
            date,
            price,
            status,
            id_traking,
            address,
            date_creation  FROM ' . _DB_PREFIX_ . 'vex_urbaner_orders Where  response != 1 ORDER BY date_creation DESC';
        }
        $sql = Db::getInstance()->ExecuteS($query);

        return $sql;
    }

    public static function getOrder($id)
    {
        $query = 'SELECT id_vex_urbaner,
       id_wsl,
        referency,
        response,
        date,
        price,
        status,
        id_traking,
        type,
        urlTracking,
        lanLot,
        vehicle_id,
        date_creation,
        address FROM ' . _DB_PREFIX_ . "vex_urbaner_orders Where  id_vex_urbaner = $id";
        $sql = Db::getInstance()->ExecuteS($query);

        return $sql;
    }

    public static function getOrderWs($id, $idWsl)
    {
        $query = 'SELECT id_vex_urbaner,
        id_wsl,
        referency,
        response,
        date,
        price,
        status,
        id_traking,
        type,
        urlTracking,
        lanLot,
        vehicle_id,
        date_creation,

        address FROM ' . _DB_PREFIX_ . "vex_urbaner_orders Where  id_vex_urbaner = $id AND id_wsl = $idWsl";
        $sql = Db::getInstance()->getRow($query);

        return $sql;
    }

    public static function getOrdersUrbaner()
    {
        $query = 'SELECT id_vex_urbaner,
        referency,
        response,
        date,
        price,
        status,
        id_traking,
        address FROM ' . _DB_PREFIX_ . 'vex_urbaner_orders';
        $sql = Db::getInstance()->ExecuteS($query);

        return $sql;
    }

    public static function getProduct($id)
    {
        $query = 'SELECT id_ws_seller,
        id_product FROM ' . _DB_PREFIX_ . "ws_seller_product Where id_product = $id";
        $sql = Db::getInstance()->getRow($query);

        return $sql;
    }

    public static function getProductWdls($id)
    {
        $query = 'SELECT id_ws_seller,
        id_product FROM ' . _DB_PREFIX_ . "ws_seller_product Where id_ws_seller = $id";
        $sql = Db::getInstance()->getRow($query);

        return $sql;
    }

    public static function getStoreWsName($name)
    {
        $query = 'SELECT id_ws_seller,
        id_customer,
        name,
        payment_acc,
        date_add,
        date_upd,
        active FROM ' . _DB_PREFIX_ . "ws_seller Where  name = '$name'";
        $sql = Db::getInstance()->getRow($query);

        return $sql;
    }

    public static function getStoreWs()
    {
        $query = 'SELECT id_ws_seller,
        id_customer,
        name,
        payment_acc,
        date_add,
        date_upd,
        active FROM ' . _DB_PREFIX_ . 'ws_seller';
        $sql = Db::getInstance()->ExecuteS($query);

        return $sql;
    }

    public static function getStoreWsId($id)
    {
        $query = 'SELECT id,
        id_ws,
        name_ws
        zip_code,
        persone,
        address,
        address_2,
        phone,
        time,
        mail,
        lat,
        lnt FROM ' . _DB_PREFIX_ . "vex_urbaner_stores WHERE id_ws = $id ";
        $sql = Db::getInstance()->getRow($query);

        return $sql;
    }

    public static function getHorarys()
    {
        $query = 'SELECT id,
        day,
        start,
        end,
        status FROM ' . _DB_PREFIX_ . 'vex_urbaner_horarys';
        $sql = Db::getInstance()->ExecuteS($query);

        return $sql;
    }

    public static function getHoraryActive()
    {
        $query = 'SELECT id,
        day,
        start,
        end,
        status FROM ' . _DB_PREFIX_ . 'vex_urbaner_horarys WHERE status = 1';
        $horarys = Db::getInstance()->ExecuteS($query);

        return $horarys;
    }

    public static function getStoreID($code)
    {
        $query = 'SELECT id,
        id_ws,
        name_ws,
        zip_code,
        persone,
        address,
        address_2,
        time,
        mail,
        phone,
        lat,
        lnt FROM ' . _DB_PREFIX_ . "vex_urbaner_stores WHERE id = '$code' ";
        $sql = Db::getInstance()->getRow($query);

        return $sql;
    }

    public static function getStores()
    {
        $query = 'SELECT id,
        id_ws,
        name_ws,
        country,
        zip_code,
        persone,
        address,
        mail,
        time,
        address_2,
        city,
        phone,
        lat,
        lnt FROM ' . _DB_PREFIX_ . 'vex_urbaner_stores ';
        $sql = Db::getInstance()->ExecuteS($query);

        return $sql;
    }

    public static function getHolidays()
    {
        $query = 'SELECT id,
        date,
        concep FROM ' . _DB_PREFIX_ . 'vex_urbaner_holidays';
        $sql = Db::getInstance()->ExecuteS($query);

        return $sql;
    }
}
