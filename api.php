<?php

$banco = new BancoDeDados('localhost', 'root', ''); //cria a classe

$banco->EXIBIR_LOGS(); //ativa a exibição dos logs no console
$banco->SALVAR_LOGS(); //ativa a gravações dos logs no arquivo log.txt
$banco->CRIAR_BANCO_DE_DADOS('meuBanco'); //cria um banco
$banco->SELECIONAR_BANCO_DE_DADOS('meuBanco2'); //seleciona o banco recen criado
$banco->CRIAR_TABELA('usuarios'); //cria uma tabela no banco selecionado
$banco->SELECIONAR_TABELA('usuarios'); //seleciona a tabela recen criada
$banco->ADD_COLUNA('nome'); //cria a coluna NOME na tabela selecionada anteriormente
$banco->ADD_COLUNA('senha'); //cria a coluna SENHA na tabela selecionada anteriormente
//Simulação de dados de um usuario:
$novoUsuarioNome = 'Eduardo Breso';
$novoUsuarioSenha = '123';
//procura se existe um registro com o nome EDUARDO, na coluna NOME
if ($banco->VALOR_EXISTE('nome', $novoUsuarioNome) === true) {
    //usuario existe, verifica login
    $idUsuario = $banco->OBTER_VALOR($novoUsuarioNome, 'nome', 'id'); //busca o ID do usuario
    $senhaUsuario = $banco->OBTER_VALOR($idUsuario, 'id', 'senha'); //busca a senha do usuario com base no ID
    //compara a senha recebida do banco com a recebida na variavel
    //perceba que no banco esta criptografada
    //então, deve-se criptografar o valor antes de comparar
    if ($banco->HASH($novoUsuarioSenha) == $senhaUsuario)
        echo 'Logado com sucesso';
    else
        echo 'Senha incorreta';
} else {
    //usuario nao existe,inicia cadastro
    $id = $banco->OBTER_ULTIMO_ID('id'); //pega o ultimo index de ID registrado
    $id++; //incrementa 1 para o novo cadastro
    $banco->INSERIR('id', $id); //registra o novo ID no banco
    $banco->DEFINIR_VALOR('id', $id, 'nome', $novoUsuarioNome); //salva os dados do NOME recebidos,no campo NOME
    $banco->DEFINIR_VALOR('id', $id, 'senha', $banco->HASH($novoUsuarioSenha)); //criptografa e salva os dados da SENHA recebidos,no campo SENHA
    echo 'Usuario cadastrado com sucesso';
}



class BancoDeDados
{
    private $host, $usuario, $senha, $nomeBanco, $nomeTabela;
    private $mostrarLogs = true;
    private $salvarLogs = false;
    public function __construct($host = 'localhost', $usuario = 'root', $senha = '')
    {
        $this->host = $host;
        $this->usuario = $usuario;
        $this->senha = $senha;
    }
    public function EXIBIR_LOGS($bool = true)
    {
        //ativa a exibição dos logs no console
        $this->mostrarLogs = $bool;
        $this->consoleLog('Logs Ativados');
    }
    public function SALVAR_LOGS($bool = true)
    {
        //ativa a gravações dos logs no arquivo log.txt
        $this->salvarLogs = $bool;
        $this->consoleLog('Logs Salvos em "log.txt" Ativados');
    }
    public function SELECIONAR_BANCO_DE_DADOS($db_nome)
    {
        if ($this->BANCO_DE_DADOS_EXISTE($db_nome))
            {
                $this->consoleLog('Banco "' . $db_nome . '" selecionado');
                $this->nomeBanco = $db_nome;}
        else {
            if ($this->mostrarLogs) {
                $this->consoleLog('Banco "' . $db_nome . '" não existe em "' . $this->host . '"');
                //error_log('Banco "' . $db_nome . '" não existe em "' . $this->host . '"');
            }
        }
    }
    public function SELECIONAR_TABELA($table_nome)
    {
        if ($this->TABELA_EXISTE($table_nome))
            {
                $this->consoleLog('Tabela "' . $table_nome . '"selecionada');
                $this->nomeTabela = $table_nome;}
        else {
            if ($this->mostrarLogs) {
                $this->consoleLog('Tabela "' . $table_nome . '" não existe em "' . $this->nomeBanco . '"');
                //error_log('Tabela "' . $table_nome . '" não existe em "' . $this->nomeBanco . '"');
            }
        }
    }
    private function consoleLog($msg, $tipo = 'n')
    {
        if ($tipo === 'n')
        echo "<script>console.log('%c".$msg."','color:green;font-weight: bold;font-size:12px;background-color:transparent');</script>";
        else
        echo "<script>console.log('%c".$msg."','color:red;font-weight: bold;font-size:12px;background-color:transparent');</script>";
        if ($this->salvarLogs) {
            if (!file_exists('log.txt')) {
                file_put_contents('log.txt', '');
            }
            date_default_timezone_set('America/Sao_Paulo');
            $data = new DateTime();
            if ($tipo === 'n')
                $msg = $data->format('[d/m/Y H:i:s] = ') . $msg;
            else
                $msg = $data->format('[d/m/Y H:i:s] ERRO = ') . $msg;

            file_put_contents('log.txt', file_get_contents('log.txt') . PHP_EOL . $msg);
        }
    }
    public function CRIAR_BANCO_DE_DADOS($nome)
    {
        //Ex:
        //CRIAR_BANCO_DE_DADOS('usuarios');
        try {
            $pdo = new PDO('mysql:host=' . $this->host, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $pdo->exec('CREATE DATABASE IF NOT EXISTS ' . $nome);
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Banco "' . $nome . '" criado com sucesso');
                //error_log('Banco "' . $nome . '" criado com sucesso');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function APAGAR_BANCO_DE_DADOS($nome)
    {
        //Ex:
        //APAGAR_BANCO_DE_DADOS('usuarios');
        try {
            $pdo = new PDO('mysql:host=' . $this->host, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $pdo->exec('DROP DATABASE IF EXISTS ' . $nome);
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Banco "' . $nome . '" deletado com sucesso');
                ////error_log('Banco "' . $nome . '" deletado com sucesso');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function CRIAR_TABELA($nomeTabela, $primaryKeyNome = 'id')
    {
        //Ex:
        //CRIAR_BANCO_DE_DADOS('usuarios');
        //CRIAR_TABELA('usuarios','dados2');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $pdo->exec('CREATE TABLE IF NOT EXISTS ' . $nomeTabela . '( 
                ' . $primaryKeyNome . ' INT AUTO_INCREMENT PRIMARY KEY
                )');
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Tabela "' . $nomeTabela . '" criada com sucesso no banco "' . $this->nomeBanco . '"');
                //error_log('Tabela "' . $nomeTabela . '" criada com sucesso no banco "' . $this->nomeBanco . '"');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function APAGAR_TABELA($nomeTabela)
    {
        //Deleta uma tabela
        //Ex:
        //APAGAR_TABELA('meudn','usuarios');
        try {
            $pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->nomeBanco, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $pdo->exec('DROP TABLE IF EXISTS ' . $nomeTabela);
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Tabela "' . $nomeTabela . '" deletada com sucesso no banco "' . $this->nomeBanco . '"');
                //error_log('Tabela "' . $nomeTabela . '" deletada com sucesso no banco "' . $this->nomeBanco . '"');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function ADD_COLUNA($nomeColuna, $tipoDeDado = 'VARCHAR(255)')
    {
        //Ex:
        //ADD_COLUNA('nome','VARCHAR(50)');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $pdo->exec('ALTER TABLE ' . $this->nomeTabela . ' ADD COLUMN IF NOT EXISTS ' . $nomeColuna . ' ' . $tipoDeDado);
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Coluna "' . $nomeColuna . '" criada na tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
                //error_log('Coluna "' . $nomeColuna . '" criada na tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function APAGAR_COLUNA($nomeColuna)
    {
        //Ex:
        //APAGAR_COLUNA('nome');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'ALTER TABLE ' . $this->nomeTabela . ' DROP COLUMN IF EXISTS ' . $nomeColuna;
            $pdo->exec($sql);
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Coluna "' . $nomeColuna . '" deletada na tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
                //error_log('Coluna "' . $nomeColuna . '" deletada na tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function OBTER_VALOR($referencia, $NomeColunaReferencia, $colunaValor)
    {
        //Ex: Obter o nome do user via id
        // echo OBTER_VALOR(1,'id','nome');
        //
        //Obter o Id do usuario pelo nome:
        //echo OBTER_VALOR('july','nome','id');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SELECT ' . $colunaValor . ' FROM ' . $this->nomeTabela . ' WHERE ' . $NomeColunaReferencia . ' = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $referencia, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(0x2);
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Valor retornado da consulta: ' . $resultado[$colunaValor]);
                //error_log('Valor retornado da consulta: ' . $resultado[$colunaValor]);
            }
            //-----------------------
            return $resultado[$colunaValor];
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function DEFINIR_VALOR($colunaId, $id, $colunaAlvo, $valor)
    {
        //Ex: Muda a senha da July pra 1020
        //DEFINIR_VALOR('nome','july','senha','1020');
        //
        //ou via ID
        //DEFINIR_VALOR('id','1','senha','1020');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'UPDATE ' . $this->nomeTabela . ' SET ' . $colunaAlvo . ' = :valor WHERE ' . $colunaId . ' = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Valor de "' . $colunaAlvo . '" setado para "' . $valor . '" na coluna "' . $colunaAlvo . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
                //error_log('Valor de "' . $colunaAlvo . '" setado para "' . $valor . '" na coluna "' . $colunaAlvo . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function VALOR_EXISTE($colunaAlvo, $valorBuscado)
    {
        try {
            $p = 'mysql:host=' . $this->host . ';dbname=' . $this->nomeBanco;
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SELECT COUNT(*) FROM ' . $this->nomeTabela . ' WHERE ' . $colunaAlvo . ' = :valor';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valorBuscado, PDO::PARAM_STR);
            $stmt->execute();
            $q = $stmt->fetchColumn();
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Item "' . $valorBuscado . '" existe = ' . $q > 0);
                //error_log('Item "' . $valorBuscado . '" existe = ' . $q > 0);
            }
            //-----------------------
            if ($q > 0)
                return true;
            else
                return false;
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function OBTER_ULTIMO_ID($colunaID)
    {
        //Returna o ultimo valor de um registro
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SELECT MAX(' . $colunaID . ') ultimoID FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $r = $stmt->fetch(0x2);
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Valor retornado: "' . $r['ultimoID'] . '"');
                //error_log('Valor retornado: "' . $r['ultimoID'] . '"');
            }
            //-----------------------
            return $r['ultimoID'];
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function INSERIR($nomeColuna, $valor)
    {
        //Cria um registro
        //
        //Ex:
        //INSERIR('id','1');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'INSERT INTO ' . $this->nomeTabela . ' (' . $nomeColuna . ') VALUES (:valor)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->execute();
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" na coluna "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
                //error_log('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" na coluna "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function APAGAR_TODAS_TABELAS()
    {
        //Deleta todas tabelas de um banco
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SHOW TABLES';
            $stmt = $pdo->query($sql);
            $tabelas = $stmt->fetchAll(0x7);
            foreach ($tabelas as $t) {
                $this->APAGAR_TABELA($t);
                //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Tabela "' . $t . '" excluida do banco "' . $this->nomeBanco . '" com sucesso');
                    //error_log('Tabela "' . $t . '" excluida do banco "' . $this->nomeBanco . '" com sucesso');
                }
                //-----------------------
            }
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function APAGAR_TODAS_COLUNAS()
    {
        //Deleta todas colunas de uma tabela
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SHOW COLUMNS FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $tabelas = $stmt->fetchAll(0x7);
            foreach ($tabelas as $t) {
                $this->APAGAR_COLUNA($t);
                //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Tabela "' . $t . '" excluida do banco "' . $this->nomeBanco . '" com sucesso');
                    //error_log('Tabela "' . $t . '" excluida do banco "' . $this->nomeBanco . '" com sucesso');
                }
                //-----------------------
            }
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function OBTER_TUDO($coluna)
    {
        /*
        Retorna um array com todos valores de uma coluna especifica
        Ex:
        $array = $suaClasse->OBTER_TUDO('nome'); 
        foreach($array as $n)
        {
            echo $n.'<br>';
        }
        */
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SELECT ' . $coluna . ' FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $tabelas = $stmt->fetchAll(0x7);
            return $tabelas;
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function MODIFICAR_TUDO($nomeColuna, $valor)
    {
        //Ex:
        //MODIFICAR_TUDO('senha','123');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'UPDATE ' . $this->nomeTabela . ' SET ' . $nomeColuna . ' = :valor';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
            $stmt->execute();
            //-----------------------
            if ($this->mostrarLogs) {
                $this->consoleLog('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" em todas colunas "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
                //error_log('Valor de "' . $nomeColuna . '" setado para "' . $valor . '" em todas colunas "' . $nomeColuna . '", tabela "' . $this->nomeTabela . '" no banco "' . $this->nomeBanco . '" com sucesso');
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function COLUNA_EXISTE($nomeColuna)
    {
        //Verifica se uma coluna existe em uma tabela
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SHOW COLUMNS FROM ' . $this->nomeTabela;
            $stmt = $pdo->query($sql);
            $colunas = $stmt->fetchAll(0x7);
            if (in_array($nomeColuna, $colunas)) { //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Coluna "' . $nomeColuna . '" existe');
                    //error_log('Coluna "' . $nomeColuna . '" existe');
                }
                //-----------------------
                return true;
            } else { //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Coluna "' . $nomeColuna . '" não existe');
                    //error_log('Coluna "' . $nomeColuna . '" não existe');
                }
                //-----------------------
                return false;
            }
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function TABELA_EXISTE($nomeTabela)
    {
        //Ex:
        //MODIFICAR_TUDO('senha','123');
        try {
            $p = 'mysql:' . 'host=' . $this->host . ';dbname=' . $this->nomeBanco . '';
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SHOW TABLES LIKE "' . $nomeTabela . '"';
            $stmt = $pdo->query($sql);
            $tb = $stmt->fetch();
            if ($tb) { //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Tabela "' . $nomeTabela . '" existe');
                    //error_log('Tabela "' . $nomeTabela . '" existe');
                }
                //-----------------------
                return true;
            } else { //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Tabela "' . $nomeTabela . '" não existe');
                    //error_log('Tabela "' . $nomeTabela . '" não existe');
                }
                //-----------------------
                return false;
            }
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
    public function BANCO_DE_DADOS_EXISTE($nomeDB)
    {
        //Ex:
        //MODIFICAR_TUDO('senha','123');
        try {
            $p = 'mysql:' . 'host=' . $this->host;
            $pdo = new PDO($p, $this->usuario, $this->senha);
            //tratamento de erros
            $pdo->setAttribute(0x3, 0x2);
            $sql = 'SHOW DATABASES LIKE "' . $nomeDB . '"';
            $stmt = $pdo->query($sql);
            $tb = $stmt->fetch();
            if ($tb) {
                //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Banco "' . $nomeDB . '" existe');
                    //error_log('Banco "' . $nomeDB . '" existe');
                }
                //-----------------------
                return true;
            } else { //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Banco "' . $nomeDB . '" não existe');
                    //error_log('Banco "' . $nomeDB . '" não existe');
                }
                //-----------------------
                return false;
            }
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
   
    public function DUPLICAR_TABELA($nomeNovaTabela)
    {
        //Duplica uma tabela e todos seus dados 
        //Ex:
        //$banco->SELECIONAR_BANCO_DE_DADOS('meuBanco');
        //$banco->SELECIONAR_TABELA('usuarios');
        //$banco->DUPLICAR_TABELA('usuarioscopiado');
        try {

            if ($this->TABELA_EXISTE($this->nomeTabela)) {
                $pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->nomeBanco, $this->usuario, $this->senha);
                //tratamento de erros
                $pdo->setAttribute(0x3, 0x2);

                $sql = 'CREATE TABLE IF NOT EXISTS ' . $nomeNovaTabela . ' LIKE ' . $this->nomeTabela;
                $pdo->exec($sql);

                $sql = 'INSERT INTO ' . $nomeNovaTabela . ' SELECT * FROM ' . $this->nomeTabela;
                $pdo->exec($sql);
                //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Tabela "' . $this->nomeTabela . '" duplicada para "' . $nomeNovaTabela . '" com sucesso');
                    //error_log('Tabela "' . $this->nomeTabela . '" duplicada para "' . $nomeNovaTabela . '" com sucesso');
                }
            } else {
                if ($this->mostrarLogs) {
                    $this->consoleLog('Tabela "' . $this->nomeTabela . '" não existe', 'erro');
                    ////error_log('Tabela "' . $this->nomeTabela . '" não existe', 'erro');
                }
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                ////error_log($e->getMessage());
            }
        }
    }




    public function APAGAR_TODOS_BANCOS_DE_DADOS()
    {
        //Duplica uma tabela e todos seus dados 
        //Ex:
        //
        try {

            $pdo = new PDO('mysql:host=' . $this->host, $this->usuario, $this->senha);
                //tratamento de erros
                $pdo->setAttribute(0x3, 0x2);


                $d = $pdo->query('SHOW DATABASES')->fetchAll(0x7);
                foreach($d as $dbs)
                {
                    if($dbs != 'information_schema' && $dbs != 'performance_schema' && $dbs!='sys')
                    {
                        echo '<br>'.$dbs;
                        $pdo->exec('DROP DATABASE IF EXISTS '.$dbs);
                    }
                }

                //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Todos bancos de dados foram excluidos com sucesso');
                    ////error_log('Todos bancos de dados foram excluidos com sucesso');
                }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                ////error_log($e->getMessage());
            }
        }
    }












    public function HASH($string)
    {
        //Criptografa uma string (usado para encriptar senhas antes de salvar no banco)
        $i = 0;
        $fim = '';
        $fim .= hash('sha512', $string);
        while ($i !== 10) {
            $fim .= hash('sha512', $string);
            $i++;
        }
        $fim = '0x' . strtoupper(hash('joaat', $fim));
        return $fim;
    }
    public function HASH_NUMEROS($string)
    {
        //Criptografa uma string (usado para encriptar senhas antes de salvar no banco)
        return preg_replace('/[a-z]+/', '', md5($string));
    }

    public function HASH_LETRAS($string)
    {
        //Criptografa uma string (usado para encriptar senhas antes de salvar no banco)
        return preg_replace('/[0-9]+/', '', md5($string));
    }




    /**
     * Nao ta funcionando e to com preguiça de corrigir
     */
    public function DUPLICAR_BANCO_DE_DADOS($nomeNovoBanco)
    {
        try {

            if ($this->BANCO_DE_DADOS_EXISTE($this->nomeBanco)) {

                if (!$this->BANCO_DE_DADOS_EXISTE($nomeNovoBanco)) {
                    $this->CRIAR_BANCO_DE_DADOS($nomeNovoBanco);
                }
                $pdoO = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->nomeBanco, $this->usuario, $this->senha);
                //tratamento de erros
                $pdoO->setAttribute(0x3, 0x2);


                // $dsnDestino = 'mysql:host=localhost;dbname=dumpBreso';
                $pdoD = new PDO('mysql:host=' . $this->host . ';dbname=' . $nomeNovoBanco, $this->usuario, $this->senha);
                //tratamento de erros
                $pdoD->setAttribute(0x3, 0x2);

                $sql = "SHOW TABLES";
                $stmt = $pdoO->query($sql);
                $tabelas = $stmt->fetchAll(0x7);

                foreach ($tabelas as $tabela) {
                    echo '<br><br>'.$tabela;
                  
                    $sqlCreate = 'CREATE TABLE IF NOT EXISTS '.$tabela.' LIKE '.$tabela.'';
                    echo '<br><br>'.$sqlCreate;
                   $pdoD->exec($sqlCreate);
            
                   
                   $sqlInsert = 'INSERT INTO '.$tabela.' SELECT * FROM '.$tabela.'';
                   echo '<br><br>'.$sqlInsert;
                   $pdoD->exec($sqlInsert);
                }


                //-----------------------
                if ($this->mostrarLogs) {
                    $this->consoleLog('Banco "' . $this->nomeBanco . '" duplicado para "' . $nomeNovoBanco . '" com sucesso');
                    //error_log('Banco "' . $this->nomeBanco . '" duplicado para "' . $nomeNovoBanco . '" com sucesso');
                }
            } else {
                if ($this->mostrarLogs) {
                    $this->consoleLog('Banco "' . $this->nomeBanco . '" não existe', 'erro');
                    //error_log('Banco "' . $this->nomeBanco . '" não existe', 'erro');
                }
            }
            //-----------------------
        } catch (PDOException $e) {
            if ($this->mostrarLogs) {
                $this->consoleLog('' . $e->getMessage(), 'erro');
                //error_log($e->getMessage());
            }
        }
    }
}














function gerarPalavra()
{
    $c = 'abcdefghijklmnopqrstuvwxyz';
    $tc = strlen($c);
    $s = '';
    for ($i = 0; $i < 10; $i++) {
        $ia = rand(0, $tc - 1);
        $s .= $c[$ia];
    }
    return $s;
}
?>