<?php 
	define( "_CONTENT_CHARSET", "utf-8" );
	mb_internal_encoding( _CONTENT_CHARSET );
	require_once 'Qiwi.php';
	$qiwi = new Qiwi('79996661212', 'a9760264ca3e817264ee2340aa877');
	// 
	
	///// баланс
	$getBalance = $qiwi->getBalance();
	echo "Баланс: ". $getBalance['accounts']['0']['balance']['amount']." р.<br>";
	/////

	///// определение id мобильного оператора
	$nmb = '9532871881';
	$getPhoneToId = $qiwi->getPhoneToId(['0' => "phone=7".$nmb]);
	echo "ID моб. оператора (".$nmb."): " .$getPhoneToId["message"]."<br>";
	/////
	
	///// комиссия   webmoney - 31271
	$getTax = $qiwi->getTax($getPhoneToId["message"]);
	echo "Комиссия от: ". $getTax['content']['terms']['commission']['ranges'][0][bound] ."р. = ". $getTax['content']['terms']['commission']['ranges'][0][rate]*100 ." %<br>";
	/////
	
	
	///// статус выплаты
	$getTxn = $qiwi->getTxn('14204809070');
	if($getTxn[status]=='SUCCESS'){$getTxn[status]='Выполнен';}
	if($getTxn[status]=='WAITING'){$getTxn[status]='Обработка';}
	if($getTxn[status]=='ERROR'){$getTxn[status]='Ошибка (код ошибки '.$getTxn[errorCode].')';}
	echo "Статус транзакции: ".$getTxn[txnId] . " = " .$getTxn['status']. "<br>";
	/////
	
	///// история транзакций
	$getHistory = $qiwi->getPaymentsHistory([
		//'startDate' => '2018-03-01T00:00:00+03:00',
		//'endDate' => '2018-10-01T00:00:00+03:00',
		'rows' => '10'
	]);
	if($getHistory){arrayHistory( $getHistory[data] );}
	function arrayHistory( array $array ) {
		echo '<br><br>История транзакций<br><table width=100%>';
		echo '<tr><td>ID</td><td>Время</td><td>Статус</td><td>Тип</td><td>Сумма</td></tr>';
		foreach( $array as $key => $val ) {
			if($val[status]=='SUCCESS'){$val[status]='Выполнен';}
			if($val[status]=='WAITING'){$val[status]='Обработка';}
			if($val[status]=='ERROR'){$val[status]='Ошибка (код ошибки '.$val[errorCode].')';}
			
			if($val[type]=='OUT'){$val[type]='Оплата '.$val[account];}
			if($val[type]=='IN'){$val[type]='Пополнение '. $val[personId];}
			if($val[type]=='QIWI_CARD'){$val[type]='Пополнение '. $val[personId];}
			
			echo '<tr><td>'.$val[txnId] .'</td>';
			echo '<td>'.strtotime($val[date]) .'</td>';
			echo '<td>'.$val[status] .'</td>';
			echo '<td>'.$val[type] .'</td>';
			echo '<td>'.$val[sum][amount] .' +  '. $val[commission][amount] .'(комиссия) = '. $val[total][amount] .'</td></tr>';
		}
		echo '</table>';
	}
	/////

	

	///// оплата /////
	$ids = time() . '000';
	$number = '9532871881';
	$summ = 10;
	$txt = 'Тестовый платеж';
	
/*	
	/// оплата на qiwi
	$sendMoney = $qiwi->sendMoneyToQiwi([
		'id' => $ids,
		'sum' => ['amount' => $summ,'currency' => '643'], 
		'paymentMethod' => ['type' => 'Account', 'accountId' => '643'],
		'comment' => $txt,
		'fields' => ['account' => '+7'.$number]
	]);
	///
*/	
	
/*
	/// оплата мобильного
	$sendMoney = $qiwi->sendMoneyToProvider($getPhoneToId,[
		'id' => $ids,
		'sum' => ['amount' => $summ,'currency' => '643'], 
		'paymentMethod' => ['type' => 'Account', 'accountId' => '643'],
		'comment' => $txt,
		'fields' => ['account' => $number]
	]);
	///
*/	

/*
	/// оплата webmoney
	$number = '319520673640';
	$sendMoney = $qiwi->sendMoneyToProvider('31271',[
		'id' => $ids,
		'sum' => ['amount' => $summ,'currency' => '643'], 
		'paymentMethod' => ['type' => 'Account', 'accountId' => '643'],
		'comment' => $txt,
		'fields' => ['account' => 'R'.$number]
	]);
	///
*/	
	
	if($sendMoney){
		if($sendMoney['transaction']['id']=='' and $sendMoney['transaction']['state']['code']==''){
			echo "Ошибка: ". $sendMoney['message']. "(код ". $sendMoney['code']. ")<br>";
		}else{
			echo "ID: ". $sendMoney['transaction']['id']. "<br>";
			echo "Статус: ". $sendMoney['transaction']['state']['code'];
		}
	}
	/////  /////


	/// разбор массива ответа
	arrayCopy( $getBalance );
	function arrayCopy( array $array ) {
		echo '<ul>';
		foreach( $array as $key => $val ) {
			echo '<li>'.$key .' = '. $val .'</li>';
			if(is_array($val)) {echo arrayCopy($val);} elseif (is_object($val)) {echo clone $val;}
		}
		echo '</ul>';
	}
	echo "<br>";
	///

?>