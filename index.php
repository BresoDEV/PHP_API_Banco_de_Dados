<?php

$banco = new BancoDeDados('localhost', 'root', ''); //cria a classe
$banco->SHOW_LOGS(); //ativa a exibição dos logs no console

$banco->CREATE_DATABASE('meuBanco'); //cria um banco
$banco->SELECT_DB('meuBanco'); //seleciona o banco recen criado

$banco->CREATE_TABLE('usuarios'); //cria uma tabela no banco selecionado
$banco->SELECT_TABLE('usuarios'); //seleciona a tabela recen criada

$banco->ADD_COLUMN('nome'); //cria a coluna NOME na tabela selecionada anteriormente
$banco->ADD_COLUMN('senha'); //cria a coluna SENHA na tabela selecionada anteriormente

//Simulação de dados de um usuario:
$novoUsuarioNome = 'Eduardo Breso';
$novoUsuarioSenha = '123';

//procura se existe um registro com o nome EDUARDO, na coluna NOME
if ($banco->IS_ITEM_EXISTS('nome', $novoUsuarioNome) === true) {
    //usuario existe, verifica login
    $idUsuario = $banco->GET_VALUE($novoUsuarioNome, 'nome', 'id'); //busca o ID do usuario
    $senhaUsuario = $banco->GET_VALUE($idUsuario, 'id', 'senha'); //busca a senha do usuario com base no ID

    //compara a senha recebida do banco com a recebida na variavel
    //perceba que no banco esta criptografada
    //então, deve-se criptografar o valor antes de comparar
    if ($banco->HASH($novoUsuarioSenha) == $senhaUsuario)
        echo 'Logado com sucesso';
    else
        echo 'Senha incorreta';
} else {
    //usuario nao existe,inicia cadastro

    $id = $banco->GET_LAST_ID('id'); //pega o ultimo index de ID registrado
    $id++; //incrementa 1 para o novo cadastro
    $banco->INSERT('id', $id); //registra o novo ID no banco
    $banco->SET_VALUE('id', $id, 'nome', $novoUsuarioNome); //salva os dados do NOME recebidos,no campo NOME
    $banco->SET_VALUE('id', $id, 'senha', $banco->HASH($novoUsuarioSenha)); //criptografa e salva os dados da SENHA recebidos,no campo SENHA
    echo 'Usuario cadastrado com sucesso';
}



class BancoDeDados
{
    private $host, $usuario, $senha, $nomeBanco, $nomeTabela;
    private $mostrarLogs = true;

    public function __construct($host = 'localhost', $usuario = 'root', $senha = '')
    {
        $this->host = $host;
        $this->usuario = $usuario;
        $this->senha = $senha;
    }

    public function SHOW_LOGS($bool = true)
    {
        $this->mostrarLogs = $bool;
    }
    public function SELECT_DB($db_nome)
    {
        if($this->IF_DATABASE_EXISTS($db_nome))
        $this->nomeBanco = $db_nome;
        else
        $this->consoleLog('Banco "' . $db_nome . '" não existe em "' . $this->host . '"');

    }

    public function SELECT_TABLE($table_nome)
    {
        if ($this->IF_TABLE_EXISTS($table_nome))
            $this->nomeTabela = $table_nome;
        else
            $this->consoleLog('Tabela "' . $table_nome . '" não existe em "' . $this->nomeBanco . '"');
    }

    private function consoleLog($msg)
    {
        echo "<script>console.log('$msg');</script>";
    }

    public function CREATE_DATABASE($nome)
    {
        //Ex:
        //CREATE_DATABASE('usuarios');

        try {
            $pdo = new PDO('mysql:host=' . $this->host, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'CREATE DATABASE IF NOT EXISTS ' . $nome;
            $pdo->exec($sql);

            $this->consoleLog('Banco "' . $nome . '" criado com sucesso');

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function DROP_DATABASE($nome)
    {
        //Ex:
        //DROP_DATABASE('usuarios');
        try {
            $pdo = new PDO('mysql:host=' . $this->host, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'DROP DATABASE IF EXISTS' . $nome;
            $pdo->exec($sql);

            $this->consoleLog('Banco "' . $nome . '" deletado com sucesso');

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function CREATE_TABLE($nomeTabela, $primaryKeyNome = 'id')
    {
        //Ex:
        //CREATE_DATABASE('usuarios');
        //CREATE_TABLE('usuarios','dados2');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'CREATE TABLE IF NOT EXISTS ' . $nomeTabela . '( 
                ' . $primaryKeyNome . ' INT AUTO_INCREMENT PRIMARY KEY
                )';
            $pdo->exec($sql);

            $this->consoleLog('Tabela "' . $nomeTabela . '" criada com sucesso no banco "' . $this->nomeBanco . '"');

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function DROP_TABLE($nomeTabela)
    {
        //Deleta uma tabela
        //Ex:
        //DROP_TABLE('meudn','usuarios');
        try {
            $pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->nomeBanco, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'DROP TABLE IF EXISTS ' . $nomeTabela;
            $pdo->exec($sql);

            $this->consoleLog('Tabela "' . $nomeTabela . '" deletada com sucesso no banco "' . $this->nomeBanco . '"');

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function ADD_COLUMN($nomeColuna, $tipoDeDado = 'VARCHAR(255)')
    {
        //Ex:
        //ADD_COLUMN('nome','VARCHAR(50)');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'ALTER TABLE ' . $this->nomeTabela . ' ADD COLUMN IF NOT EXISTS ' . $nomeColuna . ' ' . $tipoDeDado;
            $pdo->exec($sql);

            $this->consoleLog('Coluna "' . $nomeColuna . '" criada na tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function DROP_COLUMN($nomeColuna)
    {
        //Ex:
        //DROP_COLUMN('nome');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'ALTER TABLE ' . $this->nomeTabela . ' DROP COLUMN IF EXISTS' . $nomeColuna;
            $pdo->exec($sql);

            $this->consoleLog('Coluna "' . $nomeColuna . '" deletada na tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');


        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function GET_VALUE($referencia, $NomeColunaReferencia, $colunaValor)
    {
        //Ex: Obter o nome do user via id
        // echo GET_VALUE(1,'id','nome');
        //
        //Obter o Id do usuario pelo nome:
        //echo GET_VALUE('july','nome','id');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SELECT ' . $colunaValor . ' FROM ' . $this->nomeTabela . ' WHERE ' . $NomeColunaReferencia . ' = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $referencia, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->consoleLog('Valor retornado da consulta: ' . $resultado[$colunaValor]);

            return $resultado[$colunaValor];

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function SET_VALUE($colunaId, $id, $colunaAlvo, $valor)
    {
        //Ex: Muda a senha da July pra 1020
        //SET_VALUE('nome','july','senha','1020');
        //
        //ou via ID
        //SET_VALUE('id','1','senha','1020');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'UPDATE ' . $this->nomeTabela . ' SET ' . $colunaAlvo . ' = :valor WHERE ' . $colunaId . ' = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();

            $this->consoleLog('Valor de "' . $colunaAlvo . '" setado para "' . $valor . '" na coluna "' . $colunaAlvo . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');


        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }



    }

    public function IS_ITEM_EXISTS($colunaAlvo, $valorBuscado)
    {
        try {
            $p = 'mysql:host=' . $this->host . ';dbname=' . $this->nomeBanco;
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SELECT COUNT(*) FROM ' . $this->nomeTabela . ' WHERE ' . $colunaAlvo . ' = :valor';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valorBuscado, PDO::PARAM_STR);
            $stmt->execute();
            $q = $stmt->fetchColumn();

            $this->consoleLog('Item "' . $valorBuscado . '" existe = ' . $q > 0);

            if ($q > 0)
                return true;
            else
                return false;
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }


    }

    public function GET_LAST_ID($colunaID)
    {
        //Returna o ultimo valor de um registro
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SELECT MAX(' . $colunaID . ') ultimoID FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->consoleLog('Valor retornado: "' . $r['ultimoID'] . '"');

            return $r['ultimoID'];

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function INSERT($nomeColuna, $valor)
    {
        //Cria um registro
        //
        //Ex:
        //INSERT('id','1');

        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'INSERT INTO ' . $this->nomeTabela . ' (' . $nomeColuna . ') VALUES (:valor)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->execute();

            $this->consoleLog('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" na coluna "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');


        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }

    }

    public function DELETE_ALL_TABLES()
    {
        //Deleta todas tabelas de um banco
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SHOW TABLES';
            $stmt = $pdo->query($sql);
            $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tabelas as $t) {
                $this->DROP_TABLE($t);
                $this->consoleLog('Tabela "' . $t . '" excluida do banco "' . $this->nomeBanco . '" com sucesso');

            }

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }
    }


    public function DELETE_ALL_COLUMNS()
    {
        //Deleta todas colunas de uma tabela
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SHOW COLUMNS FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tabelas as $t) {
                $this->DROP_COLUMN($t);
                $this->consoleLog('Tabela "' . $t . '" excluida do banco "' . $this->nomeBanco . '" com sucesso');
            }

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }
    }


    public function GET_ALL($coluna)
    {
        /*
        Retorna um array com todos valores de uma coluna especifica

        Ex:
        $array = $suaClasse->GET_ALL('nome'); 
        foreach($array as $n)
        {
            echo $n.'<br>';
        }
        */
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SELECT ' . $coluna . ' FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $tabelas;

        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }
    }

    public function CHANGE_ALL($nomeColuna, $valor)
    {

        //Ex:
        //CHANGE_ALL('senha','123');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'UPDATE ' . $this->nomeTabela . ' SET ' . $nomeColuna . ' = :valor';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->execute();

            $this->consoleLog('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" em todas colunas "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');


        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }
    }

    public function IF_COLLUMN_EXISTS($nomeColuna)
    {
        //Verifica se uma coluna existe em uma tabela
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SHOW COLUMNS FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $colunas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (in_array($nomeColuna, $colunas))
                return true;
            else
                return false;
            //$this->consoleLog('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" em todas colunas "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');


        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }
    }

    public function IF_TABLE_EXISTS($nomeTabela)
    {
        //Ex:
        //CHANGE_ALL('senha','123');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SHOW TABLES LIKE "' . $nomeTabela . '"';
            $stmt = $pdo->query($sql);
            $tb = $stmt->fetch();
            if ($tb)
                return true;
            else
                return false;
            //$this->consoleLog('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" em todas colunas "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');


        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }
    }

    public function IF_DATABASE_EXISTS($nomeDB)
    {
        //Ex:
        //CHANGE_ALL('senha','123');
        try {
            $p = 'mysql:' . 'host=' . $this->host;
            $pdo = new PDO($p, $this->usuario, $this->senha);

            //tratamento de erros
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SHOW DATABASES LIKE "' . $nomeDB . '"';
            $stmt = $pdo->query($sql);
            $tb = $stmt->fetch();
            if ($tb)
                return true;
            else
                return false;
            //$this->consoleLog('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" em todas colunas "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');


        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage());
                error_log($e->getMessage());
            }
        }
    }

    
    public function HASH($string)
    {
        //Criptografa uma string (usado para encriptar senhas antes de salvvar no banco)
        $i=0;
        $fim ='';
        while($i!==10)
        {
            $fim .=hash('sha512',$string);
            $i++;
        }
        return hash('md5',$fim);
    }
}












function gerarPalavra()
{
    $c = 'abcdefghijklmnopqrstuvwxyz';
    $tc = strlen($c);
    $s = '';
    for ($i = 0; $i < 100; $i++) {
        $ia = rand(0, $tc - 1);
        $s .= $c[$ia];
    }
    return $s;
}

?>
