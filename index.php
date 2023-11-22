<?php

$sn = 'localhost';
$us = 'root';
$p = '';


 

//deleta todos registros onde contem o 'valor_busca' na 'noma_da_coluna'
//Ex: DeletarTudo('nome do banco','usuarios','nome','Eduardo'); deleta todo que tem esse nome
function DeletarTudo($dbn,$tabela, $noma_da_coluna, $valor_busca)
{
    global $sn;
    global $us;
    global $p;
    
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
//    Add('nome do banco','nome tabela',$colunas,$dados);
function Add($dbn,$nometabela,$arrayColunas, $arrayValores)
{
    global $sn;
    global $us;
    global $p; 

    $c = new mysqli($sn, $us, $p, $dbn);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "INSERT INTO $nometabela ($arrayColunas) VALUES ($arrayValores)";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}

//Existe('nome do banco','nome tabela','nome','Eduaro');
function Existe($dbn,$nometabela,$nomecoluna, $valor)
{
    global $sn;
    global $us;
    global $p; 

    $c = new mysqli($sn, $us, $p, $dbn);
        if ($c->connect_error) {
            echo '' . $c->connect_error . '';
        } else {
            $sql = "SELECT $nomecoluna FROM $nometabela WHERE $nomecoluna = '$valor'";
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


//ObterValor('nome do banco','nome tabela','id','23','nome');obtem o valor do campo NOME do ID 23
function ObterValor($dbn,$nometabela,$nomecolunaID, $id, $colunadesejada)
{
    global $sn;
    global $us;
    global $p;

    $c = new mysqli($sn, $us, $p, $dbn);
        if ($c->connect_error) {
            echo '' . $c->connect_error . '';
        } else {
            $sql = "SELECT $colunadesejada FROM $nometabela WHERE $nomecolunaID = '$id'";
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

//Atualizar('nome do banco','nome tabela','id', '25', 'nome','bilo');
//pega o id 25 da coluna ID e altera o campo NOME pra BILO

function Atualizar($dbn,$nometabela,$nomecolunaID, $id, $colunadesejadaAtt,$valorAtt)
{
    global $sn;
    global $us;
    global $p;

    $c = new mysqli($sn, $us, $p, $dbn);
    if ($c->connect_error) {
        echo '' . $c->connect_error . '';
    } else {
        $sql = "UPDATE $nometabela SET $colunadesejadaAtt = '$valorAtt' WHERE $nomecolunaID = '$id'";
        if ($c->query($sql)) {
            echo "feito";
        } else {
            echo $c->error;
        }
        $c->close();
    }
}

//AddColuna('biro','macacos','preco');
function AddColuna($nomeBanco,$nometabela,$nomeColuna,$tipo='VARCHAR(255)')
{
    global $sn;
    global $us;
    global $p;

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

//CriarTabela('biro','macacos');
function CriarTabela($nomeBanco,$nometabela)
{
    global $sn;
    global $us;
    global $p;

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

//CriarBanco('biro');
function CriarBanco($nomeBanco)
{
    global $sn;
    global $us;
    global $p;

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
    global $sn;
    global $us;
    global $p;

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
    global $sn;
    global $us;
    global $p;

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
