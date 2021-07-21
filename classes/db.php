<?php


class db
{
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset;
    private $pdo;

    public function __construct($host = '127.0.0.1', $db = 'sectors', $user = 'root', $pass = '', $charset = 'utf8mb4') {

        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
        $this->charset = $charset;
        $this->connect();
    }

    private function connect() {

        $rest = new PDOStatement();
        $rest->execute();
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

    }

    public function insertUserData( $name, $sectors, $terms)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sc_user_data (name, sectors, terms) 
            VALUES ( :name , :sectors , :terms)');
        $sectors = implode(',', $sectors);
        $stmt->execute( array($name, $sectors, $terms));

        return $this->pdo->lastInsertId();
    }
    public function updateUserData($id, $name, $sectors, $terms)
    {
        $stmt = $this->pdo->prepare(
            'UPDATE sc_user_data SET name = :name, sectors = :sectors, terms = :terms
            WHERE id = :id');
        $name = htmlentities($name);
        $sectors = htmlentities(implode(',', $sectors));
        $terms = htmlentities($terms);
        $stmt->execute(array($name, $sectors, $terms, $id));

        return $this->pdo->lastInsertId();
    }

    public function insertSectorData( array $values)
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sc_sectors (id_sector, name, parent) 
            VALUES ( :id_sector , :name , :parent)');
        $id_sector = $values['id_sector'];
        $name = $values['name'];
        $parent = $values['parent'];
        $stmt->execute(array($id_sector, $name, $parent));
    }

    public function loadSectorTree()
    {
        $sectorTree = array();
        $stmt = $this->pdo->prepare(
            'SELECT id_sector, name, parent FROM sc_sectors WHERE parent = 0 ORDER BY name ASC');
        $stmt->execute();
        $sectors = $stmt->fetchAll();

        foreach ($sectors as $sector) {
            $sectorTree[] = $sector;
            $depth = 0;
            $subSectors = $this->getChildren($sector['id_sector'],$depth);
            $sectorTree = array_merge($sectorTree, $subSectors);
        }
        return $sectorTree;
    }

    public function getChildren($parent, $depth)
    {
        $linestart = '&nbsp;&nbsp;&nbsp;&nbsp;';
        for ($i = 0;  $i < $depth; $i++) {
            $linestart .= $linestart;
        }
        $depth++;

        $sectorTree = array();
        $stmt = $this->pdo->prepare(
            'SELECT id_sector, name, parent FROM sc_sectors WHERE parent = '.$parent.' ORDER BY name ASC');
        $stmt->execute();
        $sectors = $stmt->fetchAll();

        foreach ($sectors as $sector) {
            $sector['name'] =  html_entity_decode($linestart . htmlentities($sector['name']));
            $sectorTree[] = $sector;
            $tempTree = $this->getChildren($sector['id_sector'], $depth);
            if (!empty($tempTree)) {
                $sectorTree = array_merge($sectorTree,$tempTree);
            }
        }

        return $sectorTree;
    }

}