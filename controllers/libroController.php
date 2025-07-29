<?php
//recibe los datos de una petición y devuelve una respuesta
class LibroController {
    private $libroDB;
    private $requestMethod;
    private $libroId;
 
    public function __construct($db, $requestMethod, $libroId = null)
    {
        $this->libroDB = new LibroDB($db);
        $this->requestMethod = $requestMethod;
        $this->libroId = $libroId;
    }

    public function processRequest(){
        //comprobar si viene la  clave _method en el objeto
        $metodo = $this->requestMethod;
        if($this->requestMethod === 'POST' && isset($_POST['_method'])){
            $metodo = strtoupper($_POST['_method']);
        }
        switch($metodo){
            case 'GET':
                if($this->libroId){
                    $respuesta = $this->getLibro($this->libroId);
                }else{
                    $respuesta = $this->getAllLibros();
                }
                break;
            case 'POST':
                $respuesta = $this->createLibro();
                break;
            case 'PUT':
                $respuesta = $this->updateLibro();
                break;
            case 'DELETE':
                $respuesta = $this->deleteLibro($this->libroId);
                break;
            default:
                $respuesta = $this->noEncontradoRespuesta();
                break;
        }

        header($respuesta['status_code_header']);
        if($respuesta['body']){
            echo $respuesta['body'];
        }
    }

    private function getAllLibros(){
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

        $filters = [
            'titulo' => isset($_GET['titulo']) ? $_GET['titulo'] : null,
            'autor' => isset($_GET['autor']) ? $_GET['autor'] : null,
            'genero' => isset($_GET['genero']) ? $_GET['genero'] : null,
            'anio' => isset($_GET['anio']) ? $_GET['anio'] : null,
            'disponible' => isset($_GET['disponible']) ? $_GET['disponible'] : null,
            'favorito' => isset($_GET['favorito']) ? $_GET['favorito'] : null,
        ];

        $result = $this->libroDB->getAllPaginated($page, $limit, $filters);
        $libros = $result['data'];
        $totalCount = $result['totalCount'];

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $libros,
            'count' => count($libros),
            'totalCount' => $totalCount,
            'page' => $page,
            'limit' => $limit
        ]);
        return $respuesta;
    }

    private function getLibro($id){
        $libro = $this->libroDB->getById($id);
        if(!$libro){
            return $this->noEncontradoRespuesta();
        }
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $libro
        ]);
        return $respuesta;
    }

    private function createLibro(){
        $input = $this->obtenerDatosRequest();

        $validacion = $this->validarDatos($input);
        if(!$validacion['valido']){
            return $this->datosInvalidosRespuesta($validacion['error']);
        }

        $nombreImagen = '';
        if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK){
            $validacionImagen = $this->validarImagen($_FILES['imagen']);
            if(!$validacionImagen['valida']){
                return $this->imagenInvalidaRespuesta($validacionImagen['mensaje']);
            }

            $nombreImagen = $this->guardarImagen($_FILES['imagen'], $input['titulo']);
            if(!$nombreImagen){
                return $this->errorGuardarImagenRespuesta();
            }
        }

        if($nombreImagen){
            $input['imagen'] = $nombreImagen;
        }

        $libro = $this->libroDB->create($input);

        if(!$libro){
            return $this->internalServerError();
        }

        $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $libro,
            'message' => 'Libro creado con éxito'
        ]);
        return $respuesta;
    }

   private function updateLibro(){
        $input = $this->obtenerDatosRequest();

        $validacion = $this->validarDatos($input);
        if(!$validacion['valido']){
            return $this->datosInvalidosRespuesta($validacion['error']);
        }

        $nombreImagen = '';
        if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK){
            $validacionImagen = $this->validarImagen($_FILES['imagen']);
            if(!$validacionImagen['valida']){
                return $this->imagenInvalidaRespuesta($validacionImagen['mensaje']);
            }

            $nombreImagen = $this->guardarImagen($_FILES['imagen'], $input['titulo']);
            if(!$nombreImagen){
                return $this->errorGuardarImagenRespuesta();
            }
            $input['imagen'] = $nombreImagen;
        }

        $libroActualizado = $this->libroDB->update($this->libroId, $input);

        if(!$libroActualizado){
            return $this->internalServerError();
        }

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'message' => 'Libro actualizado exitosamente',
            'data' => $libroActualizado
        ]);
        return $respuesta;
    }


    private function deleteLibro($id){
        $libro = $this->libroDB->getById($id);

        if(!$libro){
            return $this->noEncontradoRespuesta();
        }

        if($this->libroDB->delete($id)){
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'success' => true,
                'message' => 'Libro eliminado'
            ]);
            return $respuesta;
        }else{
            return $this->internalServerError();
        }
    }

    private function obtenerDatosRequest() {
        if(!empty($_POST['datos'])){
            return json_decode($_POST['datos'], true);
        } else {
            return json_decode(file_get_contents('php://input'), true);
        }
    }

    private function validarDatos($datos) {
        if (!isset($datos['titulo']) || empty(trim($datos['titulo']))) {
            return ['valido' => false, 'error' => 'El campo "titulo" es obligatorio y no puede estar vacío.'];
        }

        if (!isset($datos['autor']) || empty(trim($datos['autor']))) {
            return ['valido' => false, 'error' => 'El campo "autor" es obligatorio y no puede estar vacío.'];
        }

        if (!isset($datos['fecha_publicacion'])) {
            return ['valido' => false, 'error' => 'El campo "fecha_publicacion" es obligatorio.'];
        }

        $anio = $datos['fecha_publicacion'];
        $anioActual = (int)date("Y");

        if (!is_numeric($anio) || strlen((string)$anio) !== 4 || $anio < 1000 || $anio > ($anioActual + 1)) {
            return ['valido' => false, 'error' => 'El campo "fecha_publicacion" debe ser un año válido de 4 dígitos.'];
        }

        return ['valido' => true];
    }

    private function validarImagen($archivo){
        if($archivo['error'] !== UPLOAD_ERR_OK){
            return ['valida' => false, 'mensaje' => "Error al subir el archivo"];
        }

        $tamanioMaximo = 1024 * 1024;
        if($archivo['size'] > $tamanioMaximo){
            return ['valida' => false, 'mensaje' => "La imagen no puede superar 1MB"];
        }

        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if(!in_array($archivo['type'], $tiposPermitidos)){
            return ['valida' => false, 'mensaje' => "Sólo se permiten imágenes JPEG, PNG, GIF, WebP"];
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if(!in_array($extension, $extensionesPermitidas)){
            return ['valida' => false, 'mensaje' => "Extensión del archivo no permitida"];
        }

        $infoImagen = getimagesize($archivo['tmp_name']);
        if($infoImagen === false){
            return ['valida' => false, 'mensaje' => "El archivo no es una imagen válida"];
        }

        return ['valida' => true, 'mensaje' => ""];
    }

    private function noEncontradoRespuesta(){
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Libro no encontrado'
        ]);
        return $respuesta;
    }

    private function datosInvalidosRespuesta($mensaje){
        $respuesta['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => $mensaje
        ]);
        return $respuesta;
    }

    private function internalServerError(){
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Error interno del servidor'
        ]);
        return $respuesta;
    }

    private function imagenInvalidaRespuesta($mensaje){
        $respuesta['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Imagen inválida ' . $mensaje
        ]);
        return $respuesta;
    }

    private function errorGuardarImagenRespuesta(){
        $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Error al guardar la imagen en el servidor'
        ]);
        return $respuesta;
    }

    private function guardarImagen($archivo, $titulo) {
        $nombreLimpio = $this->limpiarNombreArchivo($titulo);
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nombreArchivo = $nombreLimpio . '.' . $extension;
        
        $directorioPeques = '../img/Peques/';
        $directorioGrandes = '../img/Grandes/';

        // Ensure directories exist
        if (!file_exists($directorioPeques)) {
            mkdir($directorioPeques, 0755, true);
        }
        if (!file_exists($directorioGrandes)) {
            mkdir($directorioGrandes, 0755, true);
        }

        $rutaCompletaPeques = $directorioPeques . $nombreArchivo;
        $rutaCompletaGrandes = $directorioGrandes . $nombreArchivo;

        // Save to Peques directory
        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompletaPeques)) {
            return false; // Failed to save to Peques
        }

        // Create a copy for Grandes directory (assuming it's a larger version or just a copy)
        // For a real application, you might want to resize the image for 'Grandes'
        if (!copy($rutaCompletaPeques, $rutaCompletaGrandes)) {
            // If copying to Grandes fails, you might want to log an error or handle it differently
            // For now, we'll just return the Peques filename as it was successfully saved
            return $nombreArchivo;
        }

        return $nombreArchivo;
    }

private function limpiarNombreArchivo($titulo) {
        // Convertir a minúsculas
        $nombre = strtolower($titulo);
        
        // Reemplazar caracteres especiales y espacios
        $nombre = preg_replace('/[áàäâ]/u', 'a', $nombre);
        $nombre = preg_replace('/[éèëê]/u', 'e', $nombre);
        $nombre = preg_replace('/[íìïî]/u', 'i', $nombre);
        $nombre = preg_replace('/[óòöô]/u', 'o', $nombre);
        $nombre = preg_replace('/[úùüû]/u', 'u', $nombre);
        $nombre = preg_replace('/[ñ]/u', 'n', $nombre);
        $nombre = preg_replace('/[ç]/u', 'c', $nombre);
        
        // Reemplazar espacios y caracteres no alfanuméricos con guiones bajos
        $nombre = preg_replace('/[^a-z0-9]/i', '_', $nombre);
        
        // Eliminar guiones bajos múltiples
        $nombre = preg_replace('/_+/', '_', $nombre);
        
        // Eliminar guiones bajos al inicio y final
        $nombre = trim($nombre, '_');
        
        // Limitar longitud
        if (strlen($nombre) > 50) {
            $nombre = substr($nombre, 0, 50);
            $nombre = trim($nombre, '_');
        }
        
        return $nombre ?: 'libro_sin_titulo';
    }
}


