<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
ini_set('log_errors','debug.log');

define('MSG01','※入力必須項目です。');
define('MSG02','※どれか一つ選択してください。');
define('MSG03','※メールアドレス（再入力）が合っていません。');
define('MSG04','※"! = () <> ,; : \ @"は使用できません。');
define('MSG05','※25文字以内に入力してください。');
define('MSG06','※250文字以内で入力してください。');
define('MSG07','※email形式で入力してください。');
define('MSG08','※このアドレスは既に登録済みです');
define('MSG09','※パスワードは8文字以上で入力してください');
define('MSG10','※パスワード（再入力）が合っていません。');
define('MSG11','※エラー発生中です。時間を空けてお試しください※');

$err_flg = false;
$err_msg = array();

//エラーチェック用の関数
//function errlog($str){
//	error_log("\n".$str,3,'debug.log');
//}

function dbconnect(){
	$dsn = 'mysql:dbname=junji1996_testserver; host=mysql8012.xserver.jp; charset=utf8';
	$user = 'xxxxx';
	$password = 'xxxxx';
	$options = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
	);
	//PDOオブジェクトを生成
	$dbh = new PDO($dsn,$user,$password,$options);
	return $dbh;
}

function checkEmailDup($postedEmail){
	try{
	$dbh = dbconnect();
	$sql = 'SELECT count(*) FROM users WHERE email = :email';
	$data = array(':email' => $postedEmail);
	
	$stmt = $dbh -> prepare($sql);
	$stmt -> execute($data);
	
	$result = $stmt -> fetch();
	//未登録だと0,登録済みだと1が返ってくる。SELECT countだと連想配列に数字が入ってかえってくる

	//ココが謎！emptyじゃダメ？→配列に値として「0」が入っていた。
	if(implode($result) !=0){
		global $err_msg;
		$err_msg['email']=MSG08;
	}else{
		$setEmail = $postedEmail;
		return $setEmail;
	//とりあえずこれで作動したのでOKとする。
	}
	}catch(Exception $e){
		//エラーが発生するとフォーム名の下に登録不可のメッセージを出す
		global $err_msg;
		$err_msg['fatal']= MSG11;
	}
};

//何らかのPOSTがされたら→全ての項目をまずチェックする。
if(!empty($_POST)){

	//1.Email入力のチェック
	if(empty($_POST['email'])){
		//POSTに値がない場合
		$err_msg['email'] = MSG01;
	}elseif(!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/",$_POST['email'])){
			//入力OKしかしemailの形式でない
			$err_msg['email'] = MSG07;
	}
	else{
		//email形式、データ用として一時格納→重複チェックへ→なしなら登録
		$email = checkEmailDup(htmlspecialchars($_POST['email']));
	}

	//Email重複チェックをする

	//2.ReEmail入力のチェック
	if(empty($_POST['re-email'])){
		//2-1.ReEmail未入力
		$err_msg['re-email'] = MSG01;
	}elseif($_POST['re-email'] !== $_POST['email']){
		//2-2.ReEmail入力,しかしemailと違う
		$err_msg['re-email'] = MSG03;
	}

	//2##.Passwordl入力のチェック
	if(empty($_POST['password'])){
		//POSTに値がない場合
		$err_msg['password'] = MSG01;
	}elseif(strlen($_POST['password'])<= 7){
			//入力OKしかし8文字以下である。
			$err_msg['password'] = MSG09;
	}else{
		//入力,8文字以上である。
		$password = htmlspecialchars($_POST['password']);
	}

	//2##.RePassword入力のチェック
	if(empty($_POST['re-password'])){
		//2-1.ReEmail未入力
		$err_msg['re-password'] = MSG01;
	}elseif($_POST['re-password'] !== $_POST['password']){
		//2-2.RePasswordl入力,しかしPasswordと違う
		$err_msg['re-password'] = MSG10;
	}

	//3.ニックネームの確認
	if(empty($_POST['nicname'])){
		//3-1.未入力エラー
		$err_msg['nicname'] = MSG01;
		
	}elseif(strlen($_POST['nicname'])>= 26){
			//3-2.25文字オーバー
			$err_msg['nicname'] = MSG05;
	}else{//入力あり、25文字以下→登録
		$nicname = htmlspecialchars($_POST['nicname']);
	}

	//4.学年を選択（ラジオ）
	if(empty($_POST['grade'])){
		//4-1.未選択
		$err_msg['grade'] = MSG02;
	//$_POST['grade']=1 ||$_POST['grade']=2||$_POST['grade']=3||$_POST['grade']=4)
	}else{
		//あと特殊文字含んでないかのチェック
		$grade = htmlspecialchars($_POST['grade']);
	}

	//5.登録経路を選択
	if(empty($_POST['route'])){
		//4-1.未選択
		$err_msg['route'] = MSG02;
	}else{
		//あと特殊文字含んでないかのチェック
		$route = htmlspecialchars($_POST['route']);
	}


	//6.単語の悩みを選択
	if(empty($_POST['problem-word'])){
	$err_msg['problem-word'] = MSG02;
	}else{
	//配列の文字列化→エスケープ→データ用として格納
	$word = htmlspecialchars(implode(",",$_POST['problem-word']));
	$ck_word = $_POST['problem-word'];
	}


	//7.文法の悩みを選択
	if(empty($_POST['problem-grammar'])){
	$err_msg['problem-grammar'] = MSG02;
	}else{
	$grammar = htmlspecialchars(implode(",",$_POST['problem-grammar']));
	$ck_grammar = $_POST['problem-grammar'];
	}


	//8.長文の悩みを選択
	if(empty($_POST['problem-reading'])){
	$err_msg['problem-reading'] = MSG02;
	}else{
	$reading = htmlspecialchars(implode(",",$_POST['problem-reading']));
	$ck_reading = $_POST['problem-reading'];
	}


	//9.リスニングの悩みを選択
	if(empty($_POST['problem-listening'])){
	$err_msg['problem-listening'] = MSG02;
	}else{
	$listening = htmlspecialchars(implode(",",$_POST['problem-listening']));
	$ck_listening = $_POST['problem-listening'];
	}


	//10.勉強習慣の悩みを選択
	if(empty($_POST['problem-studying'])){
	$err_msg['problem-studying'] = MSG02;
	}else{
	$studying = htmlspecialchars(implode(",",$_POST['problem-studying']));
	$ck_studying = $_POST['problem-studying'];
	}


	//11.志望校を記入
	if(empty($_POST['university'])){
		$err_msg['university'] = MSG01;
	}else{
		if(strlen($_POST['university'])>= 26){
			$err_msg['university'] = MSG05;
		}
		$university = htmlspecialchars($_POST['university']);
	}


	//12.一番解決したい内容を記入
	if(empty($_POST['worst'])){
		//12-1.未入力チェック
		$err_msg['worst'] = MSG01;
	}else{
		//12-2.25文字以内で入力
		if(strlen($_POST['worst']) > 26){
			$err_msg['worst'] = MSG05;
		}//12-3.データ用として格納
		$worst= htmlspecialchars($_POST['worst']);
	}


//13.一番解決したい内容を記入
	if(!empty($_POST['anyothers'])){
		//13-1.250文字以内で入力すること
		if(strlen($_POST['anyothers'])>=250){
			$err_msg['anyothers'] = MSG06;
		}else{
			//13-2.250文字以内で入力されていたらそれをセッション格納
			$others= htmlspecialchars($_POST['anyothers']);
		}
	}elseif(empty($_POST['anyothers'])){
		$others='メッセージなし';
	}
};


//エラーなし→情報のINSERT→メール送信
if(!empty($_POST) && empty($err_msg)){
try{
	//DBへの接続関数は20行目付近に書いてある。
	$dbh = dbconnect();
	//クエリ作成
	$stmt = $dbh -> prepare("INSERT INTO users (email, password, nicname, grade, route, problem_word, grammar, reading, listening, studying, university, worst, others) VALUES (:email,:password, :nicname,:grade,:route,:problem_word,:grammar,:reading,:listening,:studying,:university,:worst,:others)");
	
//---メモ-------------------------
//DBカラムとの違いは無し
//変数名の違いは無し
//PDO::PRAM_STRでも同じ結果になった
//SQLのカラムやデータベース名にハイフンを入れる時はクォートで囲まないといけない。でないとエラに―になる。→表示名_区切りに変更
//----------------------------

//データを挿入
	$stmt -> execute(array(
		':email' => $email,
		':password' => password_hash($password, PASSWORD_DEFAULT),
		':nicname' => $nicname,
		':grade' => $grade,
		':route' => $route,
		':problem_word' => $word,
		':grammar' => $grammar,
		':reading' => $reading,
		':listening' => $listening,
		':studying' => $studying,
		':university' => $university,
		':worst' => $worst,
		':others'=>$others));

	//INSERT後にメール送信
	$to = $email;
	$title = '登録完了しました';
	$content = <<< EOM
	登録は完了しました。
	
	登録内容は以下の通りです。
	■アドレス
	{$email}
	
	■ニックネーム
	{$nicname}
EOM;


	mb_send_mail($email,$title,$content);

	}
	catch(Exception $e){
		//エラーが発生するとフォーム名の下に登録不可のメッセージを出す
		global $err_msg;
		$err_msg['fatal']= MSG11;
}
}
?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>登録フォーム</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css?family=Kosugi+Maru&display=swap" rel="stylesheet">
    </head>
    
    <body>
       <div class="form-container">
       <div class="container-top">
        <h1>English Protocol<br>勉強法受け取りフォーム</h1>
		   <span class="err_msg fatal"><?php if(!empty($err_msg['fatal'])) echo '<p>'.$err_msg['fatal'].'</p>'; ?></span>
       </div>
        <!--フォーム全体-->
        <form method="post">
        
<!-- アドレスを入力-->
          <div class="section adress">
              <h2>メールアドレスを入力</h2>
              <!--メアドバリデーション：正しいメアド表記？ -->
              <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
              <!--メアド入力箇所↓-->
            <input type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" class="requied valid-email">
            
            <p class="caution">※Softbank,au,docomo,Ymobileのアドレスにおいて、フィルターによって勉強法送付メールが送信できない事例が報告されています。確実に勉強法を受けとるためにiCoud,gmailアドレスでの登録を強くお勧めします。なお応答のないアドレスは削除します。</p>
          </div>
       
<!--アドレスの再入力-->
           <div class="section re-adress">
            <h2>アドレス再入力お願いします。</h2>
              <!--バリデーション：正しいメアド表記？ -->
              <span class="err_msg"><?php if(!empty($err_msg['re-email'])) echo $err_msg['re-email']; ?></span>
              <!--メアド入力箇所↓-->
            <input type="text" name="re-email" placeholder="メールアドレス（再入力）" value="<?php if(!empty($_POST['re-email'])) echo $_POST['re-email']; ?>">
            
            <p class="caution">メールアドレスを間違えると勉強法が届きません。</p>
          </div>
          
          <div class="section password">
              <h2>パスワードを入力</h2>
              <!--メアドバリデーション：正しいメアド表記？ -->
              <span class="err_msg"><?php if(!empty($err_msg['password'])) echo $err_msg['password']; ?></span>
              <!--メアド入力箇所↓-->
            <input type="password" name="password" placeholder="パスワード" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>" class="requied valid-password">
            
            <p class="caution">※勉強法閲覧、相談用サイトに必要なログインパスワードを8文字以上で設定してください。</p>
          </div>
       
<!--アドレスの再入力-->
           <div class="section re-password">
            <h2>パスワード再入力お願いします。</h2>
              <!--バリデーション：正しいメアド表記？ -->
              <span class="err_msg"><?php if(!empty($err_msg['re-password'])) echo $err_msg['re-password']; ?></span>
              <!--メアド入力箇所↓-->
            <input type="password" name="re-password" placeholder="パスワード（再入力）" value="<?php if(!empty($_POST['re-password'])) echo $_POST['re-password']; ?>">
            
            <p class="caution">※誤登録を防ぐためにももう一度入力チェックをしてください。</p>
          </div>

          <div class="section nicname">
              <h2>ニックネームを入力</h2> 
            <span class="err_msg"><?php if(!empty($err_msg['nicname'])) echo $err_msg['nicname']; ?></span>
            <input type="text" name="nicname" placeholder="ニックネーム" value="<?php if(!empty($_POST['nicname'])) echo $_POST['nicname']; ?>">

            <!--バリデーション１０文字以下？ -->
            <div class="validation"></div>
            <p class="caution">相談・質問などに用います。お好きな名前を10文字以内でどうぞ。</p>
          </div>
          
<!--経由したSNSと学年を選択         -->
          <div class="basicInfo">
<!--学年を選んでもらう、浪人生は高校4年生として判別する（必須） -->
			  <div class="section grade">
				<h2>学年を選んで下さい<?php if(!empty($_POST['grade'])) echo $_POST['grade'];?></h2>
				<span class="err_msg"><?php if(!empty($err_msg['grade'])) echo $err_msg['grade']; ?></span>
				<input type="radio" name="grade" value="1年生" id="grade-1"
				<?php if(!empty($_POST['grade']) && $_POST['grade']=="1年生") echo "checked";?>>
				<label for="grade-1" >1年生</label>

				<input type="radio" name="grade" value="2年生" id='grade-2'
				<?php if(!empty($_POST['grade']) && $_POST['grade']=="2年生") echo "checked";?>>
				<label for='grade-2' >2年生</label>

				<input type="radio" name="grade" value="3年生" id="grade-3"
				<?php if(!empty($_POST['grade']) && $_POST['grade']=="3年生") echo "checked";?>>
				<label for="grade-3">3年生</label>

				<input type="radio" name="grade" value="浪人生" id="grade-4"
				<?php if(!empty($_POST['grade']) && $_POST['grade']=="浪人生") echo "checked";?>>
				<label for="grade-4">浪人生</label>
			  </div>

<!--経由したSNSなどを選択（必須）-->
			  <div class="section route">
				<h2>どちらからこのブログにたどり着きましたか？</h2> 
				<span class="err_msg"><?php if(!empty($err_msg['route'])) echo $err_msg['route']; ?></span>
				
				<input type="radio" name="route" value="Twitter" id="fromTwitter"
				<?php if(!empty($_POST['route']) && $_POST['route']=="Twitter") echo "checked";?>>
				<label for="fromTwitter">Twitter</label>

				<input type="radio" name="route" value="Instagram" id="fromInstagram"
				<?php if(!empty($_POST['route']) && $_POST['route']=="Instagram") echo "checked";?>>
				<label for="fromInstagram">Instagram</label>

				<input type="radio" name="route" value="Clear" id="fromClear"
				<?php if(!empty($_POST['route']) && $_POST['route']=="Clear") echo "checked";?>>
				<label for="fromClear">Clear</label>

				<input type="radio" name="route" value="searchEngine" id="fromSearchEngine"
				<?php if(!empty($_POST['route']) && $_POST['route']=="searchEngine") echo "checked";?>>
				<label for="fromSearchEngine">検索エンジン</label>

				<input type="radio" name="route" value="others" id="fromOthers"
				<?php if(!empty($_POST['route']) && $_POST['route']=="others") echo "checked";?>>
				<label for="fromOthers">その他</label>
			  </div>
          </div>
          
<!--単語、文法、長文、リスニングにおける悩みを選択 -->
           <div class="problems">
             <h2>各分野の当てはまる悩みを答えてください。</h2>
             <p class="caution">当てはまる悩みをタップして選択します。複数回答しても大丈夫です。悩みが特になければ「特に～～に悩みが無い場合はココにチェック」を選択してください。また選択肢に抱えている悩みが無い場合はフォーム下部の「他に伝えたいこと」に記入してお伝えください。</p>
             <div class="section word-problem">
                 <h3>単語学習の悩みを教えてください<br>(複数回答可)</h3>
                 <span class="err_msg"><?php if(!empty($err_msg['problem-word'])) echo $err_msg['problem-word']; ?></span>
                <ol>
<!--チェックボックス選択肢のPHP.配列に内容を入れれば自動で生成-->
                   <?php 
					//input内に直にckeckedを書くとなぜかHTMLでは'cheked'となってしまうのでここでチェック用の定数を定義
					$cheked_mk="checked";
					 $wordsProblemArray = array(
							"単語を覚える実感がない",
							"単語学習が面倒、つまらない",
							"実は全く単語の勉強をしていない",
							"単語を覚えることに強い苦手意識がある",
						 	"単語を勉強しても無駄な気がしてしまう。",
						 	"今の自分に適切な単語勉強法が分からない",
						 	"単語学習に時間がかかる。効率的に行いたい。",
						 	"模試・テストになると単語を忘れ、思い出せない",
						 	"単語が出来ないことが英語の苦手の原因だと思う",
						 	"単語に強い苦手意識がある"
						 );
					
					//li要素として選択肢を生成。選択された内容は配列problem-word[]としてデータとしてDBへ
					 array_push($wordsProblemArray,"特に単語で悩みが無い場合はココにチェック");
					 for($i=0; $i<count($wordsProblemArray);$i++){
						 //$_POST['problem-any']の配列と照合する。
						 if(!empty($_POST['problem-word'])&& array_search("pw{$i}",$ck_word)!== false){
							echo "<li><input type='checkbox' name='problem-word[]' value='pw{$i}' id='pw{$i}' {$cheked_mk}>
							<label for='pw{$i}' class='checkBoxDesign' >{$wordsProblemArray[$i]}</label></li>";
						 }else{
							echo "<li><input type='checkbox' name='problem-word[]' value='pw{$i}' id='pw{$i}'>
							<label for='pw{$i}' class='checkBoxDesign'>{$wordsProblemArray[$i]}</label></li>";
						 }}?>
                </ol>
             </div>         
   <!--以下単語（Word）と同じ方式-->         
            <div class="section grammar-problem">
                <h3>文法学習の悩みを教えてください<br>(複数回答可)</h3>
                <span class="err_msg"><?php if(!empty($err_msg['problem-grammar'])) echo $err_msg['problem-grammar']; ?></span>
                <ol>
                    <?php 
					 $grammarProblemArray = array(
							"文法を覚える実感がない",
						 	"実は文法の勉強を全くしていない",
						 	"文法が分からず、長文読解ができない。",
						 	"大量の文法、イディオムを覚えるのが苦手",
						 	"なぜこのような文法になるか納得できない",
							"文法学習に時間がかかる。効率的に行いたい",
							"問題集では解けるのに、本番だと解けなくなる",
							"文法をどうすれば身につけられるか分からない",
						 	"文法に強い苦手意識がある"
						 );
					 array_push($grammarProblemArray,"特に文法で悩みが無い場合はココにチェック");
					 for($i=0; $i<count($grammarProblemArray);$i++){
						 //$_POST['problem-any']の配列と照合する。
						 if(!empty($_POST['problem-grammar'])&&array_search("pg{$i}",$ck_grammar)!== false){
							echo "<li><input type='checkbox' name='problem-grammar[]' value='pg{$i}' id='pg{$i}' {$cheked_mk}>
							<label for='pg{$i}' class='checkBoxDesign' >{$grammarProblemArray[$i]}</label></li>";
						 }else{
							echo "<li><input type='checkbox' name='problem-grammar[]' value='pg{$i}' id='pg{$i}'>
							<label for='pg{$i}' class='checkBoxDesign'>{$grammarProblemArray[$i]}</label></li>";
						 }}
					?>
                </ol>
            
            </div>
            
            <div class="section reading-problem">
                <h3>長文学習の悩みを教えてください<br>(複数回答可)</h3>
                <span class="err_msg"><?php if(!empty($err_msg['problem-reading'])) echo $err_msg['problem-reading']; ?></span>
                <ol>
                    <?php
					 $readingProblemArray = array(
							"１文レベルで長文がよめない",
							"時間があっても文章全てを理解して読めない",
							"文章全ては理解できるが、時間が足りない",
							"テストになると単語を忘れてしまい、思い出せない",
							"設問内容の意図が理解できない",
							"文章の内容を覚えられず、何回も読み返してしまう",
							"分からない、忘れた単語で読解が止まってしまう",
							"長文で今にをすべきか分からない",
						 	"長文に強い苦手意識がある"
						 );
					 array_push($readingProblemArray,"特に長文学習で悩みが無い場合はココにチェック");
					 for($i=0; $i<count($readingProblemArray);$i++){
						 if(!empty($_POST['problem-reading'])&& array_search("pr{$i}",$ck_reading)!== false){
							echo "<li><input type='checkbox' name='problem-reading[]' value='pr{$i}' id='pr{$i}' {$cheked_mk}>
							<label for='pr{$i}' class='checkBoxDesign' >{$readingProblemArray[$i]}</label></li>";
						 }else{
							echo"<li><input type='checkbox' name='problem-reading[]' value='pr{$i}' id='pr{$i}'>
						 	<label for='pr{$i}' class='checkBoxDesign'>{$readingProblemArray[$i]}</label></li>";
					 }};
					?>
                </ol>
            </div>
            
            <div class="section listening-problem">
                <h3>リスニングの悩みを教えてください<br>(複数回答可)</h3>
                <span class="err_msg"><?php if(!empty($err_msg['problem-listening'])) echo $err_msg['problem-listening']; ?></span>
                <ol>
                    <?php
					 $listeningProblemArray = array(
							"短い内容でも聞き取れない",
							"長い会話が聞き取れない",
						 	"長い会話の内容を忘れてしまう",
							"リスニングで何をすべきか分からない",
						 	"実はリスニングの練習を殆どしていない",
						 	"頭の中で和訳しようとしても追いつけない",
							"日本語であっても「聞き取り」に自信がない",
						 	"リスニングに強い苦手意識がある"
						 );
					 array_push($listeningProblemArray,"特にリスニングで悩みが無い場合はココにチェック");
					 for($i=0; $i<count($listeningProblemArray);$i++){
						 if(!empty($_POST['problem-listening']) && array_search("pl{$i}",$ck_listening)!== false){
							echo "<li><input type='checkbox' name='problem-listening[]' value='pl{$i}' id='pl{$i}' {$cheked_mk}>
							<label for='pl{$i}' class='checkBoxDesign' >{$listeningProblemArray[$i]}</label></li>";
						 }else{
							echo"<li><input type='checkbox' name='problem-listening[]' value='pl{$i}' id='pl{$i}'>
						 	<label for='pl{$i}' class='checkBoxDesign'>{$listeningProblemArray[$i]}</label></li>";
					 }};
					?>
                </ol>
            </div>
            
            <div class="section studying-problem">
                <h3>勉強習慣の悩みを教えてください<br>(複数回答可)</h3>
                <span class="err_msg"><?php if(!empty($err_msg['problem-studying'])) echo $err_msg['problem-studying']; ?></span>
                <ol>
                    <?php 
					 $studyingProblemArray = array(
							"勉強の計画を立てられない",
							"勉強が続かない。3日坊主",
						 	"英語の先生の教え方が悪い",
						 	"今、英語で何をすべきか不安",
						 	"勉強を始めるまで時間がかかる",
							"勉強していても集中が続かない",
						 	"計画は立てて、途中で破綻する",
						 	"他の科目との兼ね合いがわからない",
						 	"学校、家などの学習環境に不満がある",
						 	"英語が必要なのに勉強するやる気が起きない",
						 	"部活や他の用事が忙しくて、勉強時間がない",
						 	"親からのプレッシャーが重い、指摘が煩わしい"
						 );
					 array_push($studyingProblemArray,"特に勉強習慣で悩みが無い場合はココにチェック");
					 for($i=0; $i<count($studyingProblemArray);$i++){
						 if(!empty($_POST['problem-studying']) && array_search("pst{$i}",$ck_studying)!== false){
							echo "<li><input type='checkbox' name='problem-studying[]' value='pst{$i}' id='pst{$i}' {$cheked_mk}>
							<label for='pst{$i}' class='checkBoxDesign' >{$studyingProblemArray[$i]}</label></li>";
						 }else{
							echo"<li><input type='checkbox' name='problem-studying[]' value='pst{$i}' id='pst{$i}'>
						 	<label for='pst{$i}' class='checkBoxDesign'>{$studyingProblemArray[$i]}</label></li>";
					 }};
					?>
                </ol>
            </div>
            
          </div>
          
          
           <div class="section university">
               <h2>志望校・目標を教えてください</h2>
               <span class="err_msg"><?php if(!empty($err_msg['university'])) echo $err_msg['university']; ?></span>
            <input type="text" name="university" placeholder="志望校・目標" value="<?php if(!empty($_POST['university'])) echo $_POST['university']; ?>">
            
             <p class="caution">志望校が決まっていなければ、MARCHレベルなどといいたレベルくくり、目指す偏差値などを記入してください。</p>
           </div>
            
            <div class="section worst-problem">
                <h2>今、一番解決したい英語の悩みは何ですか？</h2>
                <span class="err_msg"><?php if(!empty($err_msg['worst'])) echo $err_msg['worst']; ?></span>
                <input type="text" name="worst" placeholder="一番解決したい悩み" value="<?php if(!empty($_POST['worst'])) echo $_POST['worst']; ?>">
			</div>
            <div class="section other">
                <h2>他に伝えたいこと、ココには書いていない英語学習の悩みがあれば書いてください（任意）</h2>
                <span class="err_msg"><?php if(!empty($err_msg['anyothers'])) echo $err_msg['anyothers']; ?></span>
                <textarea name="anyothers"  cols="40" rows="10" placeholder="ここに入力" ><?php if(!empty($_POST['anyothers'])) echo $_POST['anyothers']; ?></textarea>
            </div>
            
            <div class="section submit-btn">
               <p class="caution">「送信」を押して入力アドレス宛に勉強法を送付します。<br>最後にアドレスが間違えていないかを確認してください。</p>
               <input type="submit" value="送信"> 
            </div>
            
           
        </form>
     </div>

    </body>

</html>
