<?php




 

//deleta todos registros onde contem o 'valor_busca' na 'noma_da_coluna'
//Ex: DeletarTudo('usuarios','nome','Eduardo'); deleta todo que tem esse nome
function DeletarTudo($tabela, $noma_da_coluna, $valor_busca)
{
    $sn = 'localhost';
    $us = 'root';
    $p = '';
    $dbn = 'usuarios';
    $c = new mysqli($sn, $us, $p, $dbn);
    if ($c->connect_error) {
        echo 'Erro: ' . $c->connect_error;
    } else {
        $sql = "DELETE FROM $tabela WHERE $noma_da_coluna = '$valor_busca'";
        if ($c->query($sql)) {
            echo "Ok";
        } else {
            echo $c->error;
        }

    }
    $c->close();
}

//$colunas = 'nome,senha,idade';
//   $dados = '"bbbb","cccc","22"';
//    Add($colunas,$dados);
function Add($arrayColunas, $arrayValores)
{
    $sn = 'localhost';
    $us = 'root';
    $p = '';
    $dbn = 'usuarios';

    $c = new mysqli($sn, $us, $p, $dbn);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "INSERT INTO users ($arrayColunas) VALUES ($arrayValores)";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}

//Existe('nome','Eduaro');
function Existe($nomecoluna, $valor)
{
    $sn = 'localhost';
    $us = 'root';
    $p = '';
    $dbn = 'usuarios';

    $c = new mysqli($sn, $us, $p, $dbn);
        if ($c->connect_error) {
            echo '' . $c->connect_error . '';
        } else {
            $sql = "SELECT $nomecoluna FROM users WHERE $nomecoluna = '$valor'";
            $r = $c->query($sql);
            if ($r->num_rows > 0) 
            {
                $c->close();
                echo "existe";
            }
            else 
            {
                $c->close();
                echo "n existe";
                
            }
           
        }
}


//ObterValor('id','23','nome');obtem o valor do campo NOME do ID 23
function ObterValor($nomecolunaID, $id, $colunadesejada)
{
    $sn = 'localhost';
    $us = 'root';
    $p = '';
    $dbn = 'usuarios';

    $c = new mysqli($sn, $us, $p, $dbn);
        if ($c->connect_error) {
            echo '' . $c->connect_error . '';
        } else {
            $sql = "SELECT $colunadesejada FROM users WHERE $nomecolunaID = '$id'";
            $r = $c->query($sql);
            if ($r->num_rows > 0) 
            {
               $row = $r->fetch_assoc();
               $c->close();
               echo $row[$colunadesejada];
               // var_dump($row);
            } else 
            {
                $c->close();
                echo "n existe";
                
            }
           
        }
}

//Atualizar('id', '25', 'nome','bilo');
//pega o id 25 da coluna ID e altera o campo NOME pra BILO
function Atualizar($nomecolunaID, $id, $colunadesejadaAtt,$valorAtt)
{
    $sn = 'localhost';
    $us = 'root';
    $p = '';
    $dbn = 'usuarios';

    $c = new mysqli($sn, $us, $p, $dbn);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "UPDATE users SET $colunadesejadaAtt = '$valorAtt' WHERE $nomecolunaID = '$id'";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}

AddColuna('bilo','biroleibe','preco');

function AddColuna($nomeBanco,$nometabela,$nomeColuna,$tipo='VARCHAR(255)')
{
    $sn = 'localhost';
    $us = 'root';
    $p = ''; 

    $c = new mysqli($sn, $us, $p, $nomeBanco);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "ALTER TABLE $nometabela ADD $nomeColuna $tipo";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}

function CriarTabela($nomeBanco,$nometabela)
{
    $sn = 'localhost';
    $us = 'root';
    $p = ''; 

    $c = new mysqli($sn, $us, $p, $nomeBanco);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "CREATE TABLE $nometabela(
            id INT AUTO_INCREMENT PRIMARY KEY
            )";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}
function CriarBanco($nomeBanco)
{
    $sn = 'localhost';
    $us = 'root';
    $p = '';

    $c = new mysqli($sn, $us, $p);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "CREATE DATABASE $nomeBanco";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}


function DeletarTabela($nomeBanco,$nometabela)
{
    $sn = 'localhost';
    $us = 'root';
    $p = ''; 

    $c = new mysqli($sn, $us, $p, $nomeBanco);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "DROP TABLE $nometabela";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}
function DeletarBanco($nomeBanco)
{
    $sn = 'localhost';
    $us = 'root';
    $p = '';

    $c = new mysqli($sn, $us, $p);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "DROP DATABASE $nomeBanco";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}

?>
