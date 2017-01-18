<?
$configs = array(
                "formid" => array(
                                   "Выберите форму",
                                    array(
                                          "select",
                                          DBCommon::getFromBase("id,name","forms","1","name",false),
                                          0
                                         )
                                    )
                 );
?>