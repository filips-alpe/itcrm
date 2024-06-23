<?php
class Josn extends DBObject {
    function Load() {
        switch (@self::$url[2]) {
            case 'FilterOrders':
                $this->GetFilterOrders($_GET['term']);
                die;
            case 'FilterPersons':
                $this->GetFilterPersons($_GET['term']);
                die;
            case 'FilterTypes':
                $this->GetFilterTypes($_GET['term']);
                die;
            case 'Groups':
                $this->GetGroups($_GET['term']);
                die;
            case 'Persons':
                $this->GetPersons($_GET['term']);
                die;
            case 'Types':
                $this->GetTypes($_GET['term']);
                die;
            case 'Orders':
                $this->GetOrders($_GET['term']);
                die;
            case 'Noliktava':
                $this->GetNoliktava($_GET['term']);
                die;
            case 'GetVeikals':
                $Data = Data::noliktavaDialog($_POST['ID']);
                print Template::Process('/Dialog/GetVeikals', $Data);
                die;
            case 'PrecuGrupas':
                $Data['PrecuGrupas'] = Data::PrecuGrupas($_POST['ID']);
                print Template::Process('/Dialog/PrecuGrupas', $Data);
                die;
            case 'AddFiles':
                $Data = Data::getRow($_POST['ID']);
                print Template::Process('/Dialog/AddFiles', $Data);
                die;
            case 'AddPicture':
                $Send['ID'] = $_POST['ID'];
                $Data = Data::getRow($_POST['ID']);
                if ($Data['AdminEdit'] == 1 && $_SESSION['isAdmin'] != 1) {
                    print Template::Process('/Dialog/ProtectForm', $Send);
                } else {
                    print Template::Process('/Dialog/AddPicture', $Data);
                }
                die;
            case 'AdminEditProtect':
                print $this->AdminEditProtect($_POST['ID'], $_POST['passwd']);
                die;
            case 'ErrorLogger':
                print $this->ErrorLogger($_POST);
                die;
        }
    }

    function GetFilterOrders($code) {
        $UID = $_SESSION['User']->getID();
        $Rights = Rights::getRightsArr($UID);
        //Jaaizvieto ar sesijas usera ID AND `Status`=1

        $code = substr(strrchr(", " . $code, ', '), 2);
        $query = "SELECT ID,Code FROM `Orders` WHERE Code LIKE '" . $code . "%'
                  AND ID IN (" . implode(',', $Rights['Orders']) . ")
                   ORDER BY `Code` LIMIT 0,20";
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Orders = array();
        while ($row = $result->fetch_assoc()) {
            $Orders[] = $row;
        }

        $Orders = json_encode($Orders);
        echo str_replace("Code", "label", $Orders);
    }

    function GetFilterPersons($code) {
        $UID = $_SESSION['User']->getID();
        $Rights = Rights::getRightsArr($UID);
        //Jaaizvieto ar sesijas usera ID

        $_SESSION['isAdmin'] == 1 ? $IdIn = "" : $IdIn = "AND ID IN (" . implode(',', $Rights['Persons']) . ")";

        $code = substr(strrchr(", " . $code, ', '), 2);
        $query = "SELECT ID, Login FROM `Users` WHERE Login LIKE '%" . $code . "%' AND `Status` >-3
    " . $IdIn . "
    ORDER BY `Login` LIMIT 0,30";
        //Jaizveido nosacijums ka admins cvar redzet visus respektivi ja sesija esi atzimets ka admins videjo rindu izlaizam.
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Users = array();
        while ($row = $result->fetch_assoc()) {
            $Users[] = $row;
        }

        $Users = json_encode($Users);
        echo str_replace("Login", "label", $Users);
    }

    function GetFilterTypes($code) {
        $UID = $_SESSION['User']->getID();
        $Rights = Rights::getRightsArr($UID);
        //Jaaizvieto ar sesijas usera ID AND `Status`=1

        $code = substr(strrchr(", " . $code, ', '), 2);
        $query = "SELECT ID, Code FROM `Types` WHERE Code LIKE '" . $code . "%'
                  AND ID IN (" . implode(',', $Rights['Types']) . ")
                   ORDER BY `Code` LIMIT 0,30";
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Type = array();
        while ($row = $result->fetch_assoc()) {
            $Type[] = $row;
        }

        $Type = json_encode($Type);
        echo str_replace("Code", "label", $Type);
    }

    function GetGroups($code) {
        $code = substr(strrchr(", " . $code, ', '), 2);
        $query = "SELECT id as ID, title as label FROM `groups_linear` WHERE title LIKE '%" . $code . "%'
                   ORDER BY `iorder` LIMIT 0,30";
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Type = array();
        while ($row = $result->fetch_assoc()) {
            $Type[] = $row;
        }

        $Type = json_encode($Type);
        echo $Type;
    }

    function GetPersons($code) {
        $code = substr(strrchr(", " . $code, ', '), 2);
        $query = "SELECT ID, Login FROM `Users` WHERE Login LIKE '" . $code . "%' AND `Status` > 0
                   ORDER BY `Login` LIMIT 0,30";
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Users = array();
        while ($row = $result->fetch_assoc()) {
            $Users[] = $row;
        }

        $Users = json_encode($Users);
        echo str_replace("Login", "label", $Users);
    }

    function GetTypes($code) {
        $code = substr(strrchr(", " . $code, ', '), 2);
        $query = "SELECT ID, Code FROM `Types` WHERE Code LIKE '" . $code . "%' AND `Status`=1
                   ORDER BY `Code` LIMIT 0,30";
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Type = array();
        while ($row = $result->fetch_assoc()) {
            $Type[] = $row;
        }

        $Type = json_encode($Type);
        echo str_replace("Code", "label", $Type);
    }

    function GetOrders($code) {
        $code = substr(strrchr(", " . $code, ', '), 2);
        $query = "SELECT ID,Code FROM `Orders` WHERE Code LIKE '" . $code . "%' AND `Status`=1
                   ORDER BY `Code` LIMIT 0,20";
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Orders = array();
        while ($row = $result->fetch_assoc()) {
            $Orders[] = $row;
        }

        $Orders = json_encode($Orders);
        echo str_replace("Code", "label", $Orders);
    }

    function GetNoliktava($code) {
        $query = "SELECT ID, PlaceTaken AS label FROM `Data` WHERE IDType='" . Config::Noliktava . "' AND PlaceTaken LIKE '" . $code . "%' AND `Status`=1
                   ORDER BY `PlaceTaken` LIMIT 0,20";
        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }
        $Noliktava = array();
        while ($row = $result->fetch_assoc()) {
            $Noliktava[] = $row;
        }

        $Orders = json_encode($Noliktava);
        echo $Orders;
    }

    /**
     * Funkcija paredzēta rindas bildes aizsardzībai pret izmainīšanu ja rindai ir pievienots AdminEdit statuss
     *
     * @return Template
     */
    private function AdminEditProtect($ID, $password) {
        $Data['ID'] = $ID;
        if ($password == Config::EDIT_PASS) {
            return Template::Process('/Dialog/AddPicture', $Data);
        } else {
            return Template::Process('/Dialog/ProtectFormFalse', $Data);
        }
    }

    /**
     * Funkcija saglabā javascript kļūdas ko saņem no error hendlera
     *
     * @return none
     */
    private function ErrorLogger($data) {
        $query = 'INSERT INTO `Error`
                     SET `Time`=NOW(),
                         `User`="' . $_SESSION['User']->getID() . '",
                         `Type`="' . $_SESSION['Filter']['Type'] . '",
                         `Url`="' . $data['url'] . '",
                         `Line`="' . $data['line'] . '",
                         `Message`="' . $data['message'] . '"';

        if (!self::$DB->query($query)) {
            throw new Error('Write error on Josn (' . __LINE__ . ') : ' . self::$DB->error);
        }
        return 'Kļūda failā: ' . $data['url'] . ' līnijā:' . $data['line'] . ' ar paziņojumu:' . $data['message'];
    }
}
