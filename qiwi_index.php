<?php 
	define( "_CONTENT_CHARSET", "utf-8" );
	mb_internal_encoding( _CONTENT_CHARSET );
	require_once 'Qiwi.php';
	$qiwi = new Qiwi('79102962387', '4477e3a32f722347b51d2aa890770e83');
	
	
	///// баланс
	$getBalance = $qiwi->getBalance();
	//print_r($getBalance); 
	echo "Баланс: ". $getBalance['accounts']['0']['balance']['amount']." р.";
	echo "<br>";
	/////
	
	///// определение id мобильного оператора
	$qiwi2 = new Qiwi2();
	$getPhoneToId = $qiwi2->getPhoneToId("phone=79532871881");
	print_r('ID моб. оператора: ' .$getPhoneToId); 
	echo "<br>";
	/////
	
	///// комиссия
	$qiwi3 = new Qiwi3('79102962387', '4477e3a32f722347b51d2aa890770e83');
	$getTax = $qiwi3->getTax(31271);
	//print_r($getTax); 
	echo "Комиссия от: ". $getTax['content']['terms']['commission']['ranges'][0][bound] ."р. = ". $getTax['content']['terms']['commission']['ranges'][0][rate]*100 ." %";
	echo "<br>";
	
	$summ = 20;
	$summ1 = round($getTax['content']['terms']['commission']['ranges'][0][rate]*$summ,2);
	echo $summ1."<br>";
	$summ2 = round($summ-$summ1,2);
	echo $summ2."<br>";
	
	$summ11 = round($getTax['content']['terms']['commission']['ranges'][0][rate]*$summ2,2);
	echo $summ11."<br>";
	$summ22 = round($summ2+$summ11,2);
	echo $summ22."<br>";
	/////
	
	
	//$getTax = $qiwi->getTax($getPhoneToId);
	//print_r($getTax); 
	
	///// статус выплаты
	$getTxn = $qiwi->getTxn('13513396093');
	print_r('Статус выплаты: '.$getTxn['status']); 
	//echo "<br>";
	/////
	
	///// история транзакций
	$getHistory = $qiwi->getPaymentsHistory([
	//	//'startDate' => '2018-03-01T00:00:00+03:00',
	//	//'endDate' => '2018-10-01T00:00:00+03:00',
		'rows' => '10'
	]);
	//print_r($getHistory); 
	arrayHistory( $getHistory[data] );
	function arrayHistory( array $array ) {
		echo '<table width=100%>';
		echo '<tr><td>ID</td><td>Время</td><td>Статус</td><td>Тип</td><td>Сумма</td></tr>';
		foreach( $array as $key => $val ) {
			if($val[status]=='SUCCESS'){$val[status]='Выполнен';}
			if($val[status]=='WAITING'){$val[status]='Обработка';}
			if($val[status]=='ERROR'){$val[status]='Ошибка (код ошибки'.$val[errorCode].')';}
			
			if($val[type]=='OUT'){$val[type]='Оплата '.$val[account];}
			if($val[type]=='IN'){$val[type]='Пополнение '. $val[personId];}
			if($val[type]=='QIWI_CARD'){$val[type]='Пополнение '. $val[personId];}
			
			echo '<tr><td>'.$val[txnId] .'</td>';
			echo '<td>'.strtotime($val[date]) .'</td>';
			echo '<td>'.$val[status] .'</td>';
			echo '<td>'.$val[type] .'</td>';
			echo '<td>'.$val[sum][amount] .' +  '. $val[commission][amount] .'(комиссия) = '. $val[total][amount] .'</td></tr>';
			
			//echo 'ID: '.$val[txnId] .' Время: '.strtotime($val[date]) .' Статус: '.$val[status] .' Тип: '.$val[type] .' Сумма: '.$val[sum][amount] .' +  '. $val[commission][amount] .'(комиссия) = '. $val[total][amount] .'<br>';
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
	
	if($sendMoney['transaction']['id']=='' and $sendMoney['transaction']['state']['code']==''){
		echo "Ошибка: ". $sendMoney['message']. "(код ". $sendMoney['code']. ")<br>";
	}else{
		echo "ID: ". $sendMoney['transaction']['id']. "<br>";
		echo "Статус: ". $sendMoney['transaction']['state']['code'];
	}
	/////  /////


	//arrayCopy( $sendMoneyToProvider );
	function arrayCopy( array $array ) {
		echo '<ul>';
		foreach( $array as $key => $val ) {
			echo '<li>'.$key .' = '. $val .'</li>';
			if(is_array($val)) {echo arrayCopy($val);} elseif (is_object($val)) {echo clone $val;}
		}
		echo '</ul>';
	}
	echo "<br>";
	
	
	
	
?>