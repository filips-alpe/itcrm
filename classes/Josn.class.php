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
            case 'EditSanemejs':
                $Data['Dati'] = urldecode(Pavadzime::EditSanList());
                print Template::Process('/Dialog/EditSanemejs', $Data);
                die;
            case 'GetVeikals':
                $Data = Data::noliktavaDialog($_POST['ID']);
                print Template::Process('/Dialog/GetVeikals', $Data);
                die;
            case 'PrecuGrupas':
                $Data['PrecuGrupas'] = Data::PrecuGrupas($_POST['ID']);
                print Template::Process('/Dialog/PrecuGrupas', $Data);
                die;
            case 'AddSanemejs':
                $Data = Pavadzime::LoadSanemeji($_POST['ID']);
                print urldecode(Template::Process('/Pavadzime/ChangeSanemejs', $Data));
                die;
            case 'NewSanemejs':
                print urldecode(Template::Process('/Pavadzime/NewSanemejs', $Data));
                die;
            case 'NrExist':
                echo $this->NrExist($_GET['value']);
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

    function NrExist($value) {
        $query = "SELECT * FROM `Data` WHERE IDType='72' AND IDDoc = '" . $value . "'";

        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Josn (' . __LINE__ . ')');
        }

        if ($result->num_rows > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    function getList($Data) {
        $Data = (int)$Data;

        $query = 'SELECT ID,IDDoc,Date,Note,PlaceTaken,PlaceDone FROM `Data` WHERE ID = ' . $Data;

        if (!$result = self::$DB->query($query)) {
            throw new Error('Read error on Pavadzimes (' . __LINE__ . ')');
        }
        $Info = array();

        while ($row = $result->fetch_assoc()) {
            $Info['ID'] = $row['ID'];
            $Info['IDDoc'] = $row['IDDoc'];
            $Info['Date'] = Pavadzime::date2text($row['Date']);
            $Info['IzDate'] = Pavadzime::izsnigsanastext($row['Date']);
            $Info['Note'] = $row['Note'];
            $Info['PlaceTaken'] = $row['PlaceTaken'];
            $Info['PlaceDone'] = $row['PlaceDone'];
        }

        $Sanemejs = Pavadzime::SanemejsgetAsArray();
        foreach ($Sanemejs as $k => $v)
            $Sanemejs[$k] = 'name: "' . $v . '", val:"' . $k . '"';
        $Info['Sanemejs'] = '{' . implode('},{', $Sanemejs) . '}';

        $query1 = 'SELECT ID,DocID,Samaksa,Sanemejs,Atlaide,Izsniedza,SanemejaID FROM `pavadzime` WHERE DocID = ' . $Data;

        if (!$result = self::$DB->query($query1)) {
            throw new Error('Read error on Pavadzimes (' . __LINE__ . ')');
        }

        $Info['SaveID'] = 0;
        while ($row = $result->fetch_assoc()) {
            $Info['SaveID'] = $row['ID'];
            $Info['Samaksa'] = $row['Samaksa'];
            $Info['SanemejsID'] = $row['SanemejaID'];
            $Info['Atlaide'] = $row['Atlaide'];
            $Info['Izsniedza'] = $row['Izsniedza'];
        }

        $query2 = 'SELECT ID,Nosaukums,Artikuls,Daudzums,Mervieniba,Cena FROM `pavadzime_preces` WHERE DocID = ' . $Data;

        if (!$result = self::$DB->query($query2)) {
            throw new Error('Read error on Pavadzimes (' . __LINE__ . ')');
        }

        $a = 0;
        while ($row = $result->fetch_assoc()) {
            $a++;

            $Info['tabula'] .= "<tr class=\"bordersolidadd\" id=\"$a\" name=\"" . $row['ID'] . "\">
                 <td width=\"40%\"> <input value=\"$row[Nosaukums]\" type=\"text\" class=\"Precu_nosaukums\" size=\"106\"></td>
                 <td width=\"20%\"> <input value=\"$row[Artikuls]\" type=\"text\" class=\"Artikuls\" size=\"50\"></td>
                 <td width=\"5%\"> <input value=\"$row[Daudzums]\" type=\"text\" id=\"$a\" class=\"Daudz\" size=\"16\" onblur=\"summ(this.id)\"></td>
                 <td width=\"5%\"> <input value=\"$row[Mervieniba]\" type=\"text\" class=\"Merv\" size=\"15\"></td>
                 <td width=\"10%\"> <input value=\"" . $row['Cena'] . "\" type=\"text\" id=\"$a\" class=\"Cena\" size=\"15\" onblur=\"summ(this.id)\"><a style='float:right' href='javascript:Delete(\"$a\"," . $row['ID'] . ");' class='extra delete'></a></td>
                 <td width=\"10%\" id=\"$a\" class=\"Summa\"> </td>
                    </tr>";
        }

        if ($a == 0) {
            $Info['tabula'] = "<tr ID=\"1\" class=\"bordersolidadd\" name=\"0\">
      <td width = \"40%\"> <input size = \"106\" type=\"text\" class=\"Precu_nosaukums\" /></td>
      <td width = \"20%\"> <input size = \"50\" type=\"text\" class=\"Artikuls\" /></td>
      <td width = \"5%\"> <input onblur=\"summ(this.id)\" size = \"16\" type=\"text\" class=\"Daudz\" id=\"1\"/></td>
      <td width = \"5%\"> <input size = \"15\" type=\"text\" class=\"Merv\" /></td>
      <td width = \"10%\"> <input onblur=\"summ(this.id)\" size = \"15\" type=\"text\" class=\"Cena\" id=\"1\" /><a style='float:right' href='javascript:Delete(1,0);' class='extra delete'></a></td>
      <td width = \"10%\" id=\"1\" class=\"Summa\"> </td>
    </tr>";
        }

        $Info['ierakstusk'] = $a;

        $Info['__template'] = '/Pavadzime/Supplier';
        return Template::Process($Info);
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
