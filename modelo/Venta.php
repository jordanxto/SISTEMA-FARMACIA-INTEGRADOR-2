<?php

include_once 'Conexion.php';
class Venta{

    var $objetos;
    public function __construct()
    {
        $db = new Conexion();
        $this->acceso=$db->pdo;
        
    }

function crear($cliente,$total,$fecha,$vendedor){
    $sql = "INSERT INTO venta(fecha,total,vendedor,id_cliente) values(:fecha,:total,:vendedor,:cliente)";
    $query = $this->acceso->prepare($sql);
    $query->execute(array(':fecha' => $fecha,':cliente' => $cliente,':total' => $total,':vendedor' => $vendedor));
    return "creado";
}

function ultima_venta(){
    $sql = "SELECT MAX(id_venta) as ultima_venta FROM venta";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function borrar($id_venta){
    $sql = "DELETE FROM venta WHERE id_venta=:id_venta";
    $query = $this->acceso->prepare($sql);
    $query->execute(array(':id_venta' => $id_venta));
    echo 'delete';
    return "borrado";
}

function buscar(){
    $sql = "SELECT id_venta,fecha,cliente,dni,total,CONCAT(usuario.nombre_us,' ',usuario.apellidos_us) as vendedor,id_cliente FROM venta join usuario on vendedor=id_usuario";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function verificar($id_venta,$id_usuario){
    $sql = "SELECT * FROM venta WHERE vendedor=:id_usuario and id_venta=:id_venta";
    $query = $this->acceso->prepare($sql);
    $query->execute(array(':id_usuario'=>$id_usuario,':id_venta'=>$id_venta));
    $this->objetos = $query->fetchall();
    if(!empty( $this->objetos)){
        return 1;

    }
    else{
        return 0;
    }
}

function recuperar_vendedor($id_venta){
    $sql = "SELECT us_tipo FROM venta join usuario on id_usuario=vendedor where id_venta=:id_venta";
    $query = $this->acceso->prepare($sql);
    $query->execute(array(':id_venta'=>$id_venta));
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function venta_dia_vendedor($id_usuario){
    $sql = "SELECT sum(total) as venta_dia_vendedor  FROM `venta` WHERE vendedor=:id_usuario AND date(fecha)=date(curdate())";
    $query = $this->acceso->prepare($sql);
    $query->execute(array(':id_usuario'=>$id_usuario));
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function venta_diaria(){
    $sql = "SELECT sum(total) as venta_diaria FROM `venta` WHERE date(fecha)=date(curdate())";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function venta_mensual(){
    $sql = "SELECT sum(total) as venta_mensual FROM `venta` WHERE year(fecha)=year(curdate()) and month(fecha)=month(curdate())";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function monto_costo(){
    $sql = "SELECT SUM(det_cantidad*precio_compra) as monto_costo FROM detalle_venta
    join venta on id_det_venta=id_venta and year(fecha)=year(curdate()) and month(fecha)=month(curdate())
    join lote on id_det_lote=lote.id";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function venta_anual(){
    $sql = "SELECT sum(total) as venta_anual FROM `venta` WHERE year(fecha)=year(curdate())";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function buscar_id($id_venta){
    $sql = "SELECT id_venta,fecha,cliente,dni,total,CONCAT(usuario.nombre_us,' ',usuario.apellidos_us) as vendedor, id_cliente FROM venta join usuario on vendedor=id_usuario
    and id_venta=:id_venta";
    $query = $this->acceso->prepare($sql);
    $query->execute(array(':id_venta'=>$id_venta));
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function venta_mes(){
    $sql = "SELECT sum(total) as cantidad,month(fecha) as mes FROM `venta` WHERE year(fecha)=year(curdate()) GROUP by month(fecha)";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function vendedor_mes(){
    $sql = "SELECT CONCAT(usuario.nombre_us,' ',usuario.apellidos_us) as vendedor_nombre, ROUND(sum(total),2) as cantidad FROM `venta` join usuario on id_usuario=vendedor WHERE month(fecha)=month(curdate()) and year(fecha)=year(curdate()) GROUP by vendedor order by cantidad DESC LIMIT 3";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function ventas_anual(){
    $sql = "SELECT sum(total) as cantidad,month(fecha) as mes FROM `venta` WHERE year(fecha)=year(date_add(curdate(),INTERVAL -1 YEAR)) GROUP by month(fecha)";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function producto_mas_vendido(){
    $sql = "SELECT nombre,concentracion,adicional,sum(cantidad) as total FROM `venta`
    JOIN venta_producto ON id_venta=venta_id_venta
    JOIN producto ON id_producto=producto_id_producto
    WHERE year(fecha)=year(curdate()) AND month(fecha)=month(curdate())
    GROUP BY producto_id_producto ORDER BY total DESC LIMIT 5";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

function cliente_mes(){
    $sql = "SELECT CONCAT(cliente.nombre,' ',cliente.apellidos) as cliente_nombre, ROUND(sum(total),2) as cantidad 
    FROM `venta` 
    join cliente on id_cliente=id 
    WHERE month(fecha)=month(curdate()) 
    and year(fecha)=year(curdate()) 
    GROUP by id_cliente 
    order by cantidad 
    DESC LIMIT 3";
    $query = $this->acceso->prepare($sql);
    $query->execute();
    $this->objetos = $query->fetchall();
    return $this->objetos;
}

}