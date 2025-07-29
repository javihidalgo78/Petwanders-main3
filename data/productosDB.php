<?php
/**
 * Se encarga de interactuar con la base de datos
 */
class LibroDB
{
    private $db;
    private $table = 'productos';
    //recibe una conexión ($database) a una base de datos y la mete en $db
    public function __construct($database)
    {
        $this->db = $database->getConexion();
    }

    //extrae todos los datos de la tabla $table
    public  function getAll(){
        //construye la consulta
        $sql = "SELECT * FROM {$this->table}";
        //$sql = "SELECT * FROM {$this->table} ORDER BY DESC";

        //realiza la consulta con la función query()
        $resultado = $this->db->query($sql);

        //comprueba si hay respuesta ($resultado) y si la respuesta viene con datos
        if($resultado && $resultado->num_rows > 0){
            //crea un array para guardar los datos
            $productos = [];
            //en cada vuelta obtengo un array asociativo con los datos de una fila y lo guardo en la variable $row
            //cuando ya no quedan filas que recorrer termina el bucle
            while($row = $resultado->fetch_assoc()){
                //al array libros le añado $row 
                $productos[] = $row;
            }
            //devolvemos el resultado
            return $productos;
        }else{
            //no hay datos, devolvemos un array vacío
            return [];
        }
        
    }  
}

