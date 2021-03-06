<?php 
    require($_SERVER['DOCUMENT_ROOT'] . "/horadocafe/php/conn.php");

    class PedidoDAO{
        function insert($pedido){
            $conexao = conectar();
            $query = "INSERT INTO Pedido(emailUsuario,descricao,data,preco) VALUES (?,?,?,?);";
            $stmt = mysqli_prepare($conexao,$query);
            mysqli_stmt_bind_param($stmt,"sssd",$pedido['emailUsuario'],$pedido['descricao'],$pedido['data'],$pedido['preco']);
            executar_SQL($conexao,$stmt);
            desconectar($conexao);

            $idPedido = $this->lastId() - 1;

            $conexao2 = conectar();

            foreach($pedido['produtos'] as $produto){
                $query2 = "INSERT INTO Pedido_Produto(idPedido,idProduto,quantidade) VALUES (?,?,?);";
                $stmt2 = mysqli_prepare($conexao2,$query2);
                mysqli_stmt_bind_param($stmt2,"iii",$idPedido,$produto['idproduto'],$produto['quantidade']);
                executar_SQL($conexao2,$stmt2);
            }

            desconectar($conexao);
        }

        function atender($id){
            $conexao = conectar();
            $query = "UPDATE Pedido SET atendido = 1 WHERE id = ?;";
            $stmt = mysqli_prepare($conexao,$query);
            mysqli_stmt_bind_param($stmt,"i",$id);
            executar_SQL($conexao,$stmt);
            desconectar($conexao);
        }

        function remove($id){
            $conexao = conectar();
            $query = "DELETE FROM Pedido_Produto WHERE idPedido = ?;";
            $query2 = "DELETE FROM Pedido WHERE id = ?;";
            $stmt = mysqli_prepare($conexao,$query);
            $stmt2 = mysqli_prepare($conexao,$query2);
            mysqli_stmt_bind_param($stmt,"i",$id);
            mysqli_stmt_bind_param($stmt2,"i",$id);
            executar_SQL($conexao,$stmt);
            executar_SQL($conexao,$stmt2);
            desconectar($conexao);
        }

        function listar(){
            $conexao = conectar();
            $query = "SELECT * FROM Pedido WHERE atendido = 0 AND DATE(data) = CURDATE() ORDER BY data ASC;";
            $stmt = mysqli_prepare($conexao,$query);
            $resultado = executar_SQL($conexao,$stmt);
            desconectar($conexao);

            $pedidos = lerResultado($resultado);

            foreach($pedidos as $chave => $pedido){
                $pedidos[$chave]['produtos'] = [];
                $pedidos[$chave]['produtos'] = $this->getProdutos($pedido['id']);
            }

            return $pedidos;
        }

        function getProdutos($id){
            $conexao = conectar();
            $query = "SELECT * FROM Pedido_Produto WHERE idPedido = ?;";
            $stmt = mysqli_prepare($conexao,$query);
            mysqli_stmt_bind_param($stmt,"i",$id);
            $resultado = executar_SQL($conexao,$stmt);
            desconectar($conexao);

            return lerResultado($resultado);
        }

        function get($id){
            $conexao = conectar();
            $query = "SELECT * FROM Pedido WHERE id = ?;";
            $stmt = mysqli_prepare($conexao,$query);
            mysqli_stmt_bind_param($stmt,"i",$id);
            $resultado = executar_SQL($conexao,$stmt);
            desconectar($conexao);

            $arrayResultado = lerResultado($resultado);

            if(count($arrayResultado) > 0){
                $pedido = $arrayResultado[0];
                $pedido['produtos'] = $this->getProdutos($id);
            }

            return $pedido;
        }

        function lastId(){
            $conexao = conectar();
            $query = "SELECT auto_increment FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'pedido';";
            $stmt = mysqli_prepare($conexao,$query);
            $resultado = executar_SQL($conexao,$stmt);
            desconectar($conexao);

            return lerResultado($resultado)[0]['auto_increment'];
        }
    }
?>