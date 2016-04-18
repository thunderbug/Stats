<?php
include_once 'MySQL.php';

class TableBuilder {
	
	public function build($data){
		
		$cols = $this->columnNames($data[0]);
		$numc = count($cols);
		$string = '';
		$i = 0;
		
		foreach ($data as $row){

			$string .= '<tr class=';
			$string .= ($i%2==0) ? '"row1"' : '"row2"';
			$string .= ">\n";
			for($j = 0; $j<$numc; $j++){
                //Maybe i should have done it better then this way but heres a dirty fix, love thunder
                if($cols[$j] != "Flag" && $cols[$j] != "ip" && $cols[$j] != "name_id") {
                    $val = ($row[$cols[$j]]) ? $row[$cols[$j]] : "0";

                    if ($cols[$j] == "name") {
                        $string .= "<td class='" . $cols[$j] . "'>";
                        if($row["Flag"] != "")
                        		$string .= "<img src=\"https://cp.justforfun-gaming.com/template/jff/img/flags/" . $row["Flag"] . ".png\">&nbsp;";
                        $string .= "<a href='".URL."game/mw2/action/usr/user/".$row['name_id']."'>" . $val . "</a></td>\n";
                    } else {
                        $string .= "<td class='" . $cols[$j] . "'>" . $val . "</td>\n";
                    }
                }
                //end

			}
			$string .= "</tr>\n";
			$i++;
		}
		return $string;
	}	
	
	public function columnNames($data){
		return array_keys($data);
	}
}