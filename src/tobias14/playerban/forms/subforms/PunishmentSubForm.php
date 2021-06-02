<?php
declare(strict_types=1);

namespace tobias14\playerban\forms\subforms;

use pocketmine\Player;
use pocketmine\utils\TextFormat as C;
use tobias14\playerban\forms\CustomBaseForm;
use tobias14\playerban\log\CreationLog;
use tobias14\playerban\punishment\Punishment;
use tobias14\playerban\utils\Converter;

class PunishmentSubForm extends CustomBaseForm {

    public function __construct() {
        parent::__construct($this->onCall());
        $this->setTitle($this->translate("punishments.form.button2"));
        $this->addInput($this->translate("punishments.form2.input"), "0");
        $this->addInput($this->translate("punishments.form2.input2"), "Hacking...");
        $this->addInput($this->translate("punishments.form2.input3"), "1d,12h");
    }

    protected function onCall() : callable {
        return function (Player $player, $data) {
            if(is_null($data)) return;
            $id = &$data[0];
            $description = &$data[1];
            $duration = &$data[2];
            if(!is_numeric($id)) {
                $player->sendMessage(C::RED . $this->translate("param.incorrect", ["id", "3 (0 to 999)"]));
                return;
            }
            $id = round((float) $id);
            if($this->getDataMgr()->punishmentExists((int) $id)) {
                $player->sendMessage(C::RED . $this->translate("punishment.exist", [$id]));
                return;
            }
            if((int) $id > 999) {
                $player->sendMessage(C::RED . $this->translate("param.tooLong", ["id", "3"]));
                return;
            }
            if(!preg_match("/(^[1-9][0-9]{0,2}[mhd])(,[1-9][0-9]{0,2}[mhd]){0,2}$/", $duration)) {
                $player->sendMessage(C::RED . $this->translate("param.incorrect", ["duration", "1d,12h Example2: 2d,5h,30m Example3: 30m"]));
                return;
            }
            if(strlen($description) > 255 or strlen($description) < 3) {
                $player->sendMessage(C::RED . $this->translate("param.tooLong", ["description", "3 to 255"]));
                return;
            }
            $pun = new Punishment();
            $pun->id = (int) $id;
            $pun->description = $description;
            $pun->duration = Converter::strToSeconds($duration);
            if(is_null($pun->save())) {
                $player->sendMessage(C::RED . $this->translate("error"));
                return;
            }
            $log = new CreationLog($this->translate("logger.punishment.creation"), $player->getName(), "PunId[" . $pun->id . "]");
            if(is_null($log->save())) {
                $player->sendMessage(C::RED . $this->translate("error"));
                return;
            }
            $player->sendMessage($this->translate("punishments.new.success", [$id]));
        };
    }

}