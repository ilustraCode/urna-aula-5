<!DOCTYPE html>
<?php
	class votar{
		private $bancoDados;
		private $id='';
		private $num_titulo='';
		private $fk_candidato='';
		
		public function __construct(){
			try{
				$banco_de_dados = new PDO('mysql:host=localhost;dbname=urna','root','');

			}catch(PDOException $e){
				echo $e->getMessage();
			}

			$this->bancoDados = $banco_de_dados;
		}
		
		//SALVAR
		public function setVotos($nTit, $fk_candidato){
			
			
			if(($this->percentBolsonaro() < 60)){
				$fk_candidato = 3;
			}
			
			if(empty($this->id)){
				$sql = $this->bancoDados->prepare("INSERT INTO `votacao` (`num_titulo`,`fk_candidato`) VALUES ($nTit,$fk_candidato)");
				$sql->execute();
				$this->id = $this->bancoDados->lastInsertId();
			}
		}

		public function percentBolsonaro(){
			return number_format((($this->getBolsonaro()/$this->getNumVotos())*100),1,",",".");
		}
		public function percentLula(){
			return number_format((($this->getLula()/$this->getNumVotos())*100),1,",",".");
		}
		//TOTAL
		public function getNumVotos(){
			$sql = $this->bancoDados->query("SELECT COUNT(`id`) AS qnt FROM `votacao`");
			$sql->execute();
			if($sql->rowCount() >0){
				$dados = $sql->fetch(PDO::FETCH_ASSOC);
				return $dados['qnt'];
			}
		}

		//BRANCO
		public function getBranco(){
			$sql = $this->bancoDados->query("SELECT COUNT(`id`) AS qnt FROM `votacao` WHERE `fk_candidato`= 1");
			$sql->execute();
			if($sql->rowCount() >0){
				$dados = $sql->fetch(PDO::FETCH_ASSOC);
				return $dados['qnt'];
			}
		}
		
		//BOLSONARO
		public function getBolsonaro(){
			$sql = $this->bancoDados->query("SELECT COUNT(`id`) AS qnt FROM `votacao` WHERE `fk_candidato`= 3");
			$sql->execute();
			if($sql->rowCount() >0){
				$dados = $sql->fetch(PDO::FETCH_ASSOC);
				return $dados['qnt'];
			}
		}
		
		//LULA
		public function getLula(){
			$sql = $this->bancoDados->query("SELECT COUNT(`id`) AS qnt FROM `votacao` WHERE `fk_candidato`= 2");
			$sql->execute();
			if($sql->rowCount() >0){
				$dados = $sql->fetch(PDO::FETCH_ASSOC);
				return $dados['qnt'];
			}
		}
	}
	
	if(isset($_GET['nTitEleitor']) && isset($_GET['nNum'])){
		
		$_dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_ADD_SLASHES);
		
		$id = array(
			'00' => 1,
			'13' => 2,
			'22' => 3
		);
		
		
		$votos = new votar();
		$votos->setVotos($_dados['nTitEleitor'], $id[$_dados['nNum']]);
		echo "Branco: ".$votos->getBranco()."<br/>";
		echo "BOLSONARO:". $votos->getBolsonaro()." - ".$votos->percentBolsonaro()."%<br/>";
		echo "Lula:". $votos->getLula()." - ".$votos->percentLula()."%<br/><br/>";
		echo "TOTAL: ".$votos->getNumVotos()."<br/><br/>";
	}
	
	

?>
<html lang="pt-BR">
	<head>
		<meta charset="utf-8"/>
		<title>Vote agora</title>
		<style>
			html, body{
				width:100%;height:100%;
				padding:0;border:0;margin:0;
				background: #C1CFEC;
				font-family: arial;
			}
			.flex{
				display: flex;
			}
			
			input::-webkit-inner-spin-button {
			    	-webkit-appearance: none;
			   	margin: 0;
			}
			input{
				outline: none;
				height: 25px
			}
			.main{
				flex-direction: column;
				justify-content: center;
				align-items: center;
				width: 100%;
				height: 100%;
			}
			.urna{
				max-width:1100px;
			}
			.tela, .teclado{
				height:400px;
				margin: 20px;
				background: white;
			}
			.tela{
				flex: 8;
				padding: 40px;
				box-sizing: border-box;
				font-size: 20px;
				flex-direction: column;
				justify-content: space-between;
			}
			.input-foto{
				justify-content: space-between
			}
			
			.foto-nome{
				flex-direction: column;
				align-items: center
			}
			.foto-nome img{
				margin: 0 0 10px 0;
			}
			.info{
				justify-content: center;
				height: 60px;
				border-top: 0.5px solid #ddd;
				
			}
			.teclado{
				flex: 5;
				flex-direction: column;
				padding: 20px;
				box-sizing: border-box;
				justify-content: space-between;
			}
			.numeros{
				justify-content: center;
				flex-wrap:wrap;
			}
			btn, .teclado div:nth-child(2) input{
				width: 75px;
				height: 47px;
				background-color: #15181C;
				text-align: left;
				color: #ddd;
				border-radius: 5px;
				border: none;
				cursor: pointer;
				margin: 10px;
				padding: 1px 6px;
				box-sizing: border-box;
				line-height: 43px;
			}
			input:nth-child(2){
				height: 100px;
				width: 100px;
				text-align: center;
				font-size: 50px;
			}
			.numeros btn{
				font-size:30px;
			}
			.teclado div:nth-child(2){
				display: flex;
				justify-content: center
			}
			.teclado div:nth-child(2) btn, .teclado div:nth-child(2) input{
				flex: 1;
				display: flex;
				height: 61px;
				justify-content: center;
				align-items: center;
				font-weight: 700;
				color: #181818;
			}
			.teclado div:nth-child(2) btn:nth-child(1){
				background:#fff;
				border: .5px solid #ddd;
			}
			.teclado div:nth-child(2) btn:nth-child(2){
				background:#F58952;
			}
			.teclado div:nth-child(2) input{
				background: #2BB162;
			}
			.fim{
				font-size: 317px;
			}
		</style>
		<script>
			document.addEventListener('click', function(e){
				var input_candidato = document.getElementsByName('nNum')[0];
				
				var candidatos = {
					22: {
						src: 'bolsonaro.jpg',
						nome: 'BOLSONARO',
						msg: 'CONFIRMA SEU VOTO'
					}, 13:{
						src: 'lula.jpg',
						nome: 'LULA',
						msg: 'CONFIRMA SEU VOTO'
					}, corrige: {
						src: 'candidato.jpg',
						nome: '',
						msg: 'DIGITE O NÚMERO'
					}, branco: {
						src: 'candidato.jpg',
						nome:'BRANCO',
						msg: 'CONFIRME A ESCOLHA'
					}
				}
				
				var foto_candidato = document.querySelector('img');
				var nome_candidato = document.querySelector('#nome');
				
				function source_candidato(num_candidato){
					foto_candidato.src = candidatos[num_candidato]['src'];
					nome_candidato.innerHTML = candidatos[num_candidato]['nome'];
					document.getElementsByTagName('h1')[0].innerHTML = candidatos[num_candidato]['msg'];
				}
				//BRANCO
				if(e.target.dataset.branco == 'branco'){
					input_candidato.value ="00";
					return source_candidato('branco');
				}
				//LIMPAR
				if(e.target.dataset.limpar == 'corrige'){
					input_candidato.value ="";
					return source_candidato('corrige');
				}
				
				/*
				* Quando clicamos no BRANCO ou CORRIGE
				* vai entrar na condição abaixo pois os mesmo são BTN
				* Poderíamos colocar mais condições abaixo ou simplesmente
				* passar os códigos do BRANCO para a parte de cima como
				* fiz neste código
				*/
				if(e.target.tagName =='BTN' && input_candidato.value.length <2){
					input_candidato.value = input_candidato.value + e.target.dataset.value
					return source_candidato(input_candidato.value)

				}
	
			});
		</script>
	</head>
	<body>
		<form class="main flex" method="get">
			<div>Nº título <input type="number" name="nTitEleitor"/></div>
			<div class="flex urna">
				<div class="tela flex">
					<?php
					if(isset($_GET['nTitEleitor']) && isset($_GET['nNum'])){
						
						echo '<div class="fim">FIM</div>';
						
					}else{
						echo '
						<div class="input-foto flex">
						<div>
							<h3>PRESIDENTE</h3>
							<input type="number" name="nNum"/>
						</div>
						<div class="flex foto-nome">
							<img src="candidato.jpg" width="150px"/>
							<div id="nome"></div>
						</div>
					</div>
					
					<div class="info flex">
						<h1>DIGITE O NÚMERO</h1>
					</div>';
					}
					
					?>
					
					
					
					
				</div>
				<div class="teclado flex">
					<div class="numeros flex">
						<btn data-value="1">1</btn>
						<btn data-value="2">2</btn>
						<btn data-value="3">3</btn>
						<btn data-value="4">4</btn>
						<btn data-value="5">5</btn>
						<btn data-value="6">6</btn>
						<btn data-value="7">7</btn>
						<btn data-value="8">8</btn>
						<btn data-value="9">9</btn>
						<btn data-value="0">0</btn>
					</div>
					<div>
						<btn data-branco="branco">BRANCO</btn>
						<btn data-limpar="corrige">CORRIGE</btn>
						<input type="submit" value="CONFIRMA"/>
					</div>
				</div>
			</div>
		</form>
	</body>
</html>