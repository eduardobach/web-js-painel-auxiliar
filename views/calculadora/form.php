<style type="text/css">
	.nopadding {
	   padding: 0 !important;
	   margin: 0 !important;
	}

</style>
<div class="row">
	<div class="col-lg-2" id="estrutura-calc">
		<div class="panel panel-default">
	        <div class="panel-heading">Calculadora</div>
	        <div class="panel-body">

				<div class="row">
					<div class="col-lg-12 nopadding"><input class="form-control" id="result-calc"></div>
				</div>
				<div class="row">
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="7">7</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="8">8</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="9">9</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-default btn-num-calc" data-comando="+">+</button>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="4">4</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="5">5</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="6">6</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-default btn-num-calc" data-comando="-">-</button>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="1">1</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="2">2</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="3">3</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-default btn-num-calc" data-comando="*">X</button>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-warning btn-num-calc" data-comando="c">C</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-info btn-num-calc" data-comando="0">0</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-default btn-num-calc" data-comando="=">=</button>
					</div>
					<div class="col-xs-3 nopadding">
						<button type="button" class="btn btn-lg btn-block btn-default btn-num-calc" data-comando="/">/</button>
					</div>
				</div>
	        </div>
	        <div class="panel-footer"></div>
	    </div>
	</div>

	<div class="col-lg-4" id="estrutura-imc">
		<div class="panel panel-default">
	        <div class="panel-heading">IMC</div>
	        <div class="panel-body">

		        <TABLE BORDER=4 WIDTH="350" bgcolor="green">
	 				<TR>
						<TH ALIGN="CENTER" WIDTH="100">IMC</TH>
						<TH ALIGN="CENTER" WIDTH="250">Classificação</TH>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER"><16</TD>
                        <TD ALIGN="CENTER">Desnutrição grau 3</TD>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER">16 ~ 16,9</TD>
                        <TD ALIGN="CENTER">Desnutrição grau 2</TD>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER">17 ~ 18,4</TD>
                        <TD ALIGN="CENTER">Desnutrição grau 1</TD>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER">18,5 ~ 24,9</TD>
                        <TD ALIGN="CENTER">Peso Ideal</TD>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER">25 ~ 29,9</TD>
                        <TD ALIGN="CENTER">Levemente Acima do Peso</TD>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER">30 ~ 34,9</TD>
                        <TD ALIGN="CENTER">Obesidade Grau 1</TD>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER">35 ~ 39,9</TD>
                        <TD ALIGN="CENTER">Obesidade Grau 2 (severa)</TD>
	                </TR>
	                <TR>
                        <TD ALIGN="CENTER">>40</TD>
                        <TD ALIGN="CENTER">Obesidade Grau 3 (mórbita)</TD>
	                </TR>
	                <TR>
						<TD ALIGN="RIGHT">Altura(m):</TD>
						<TD><input class="form-control" id="altura-imc"></TD>
	                </TR>
	                <TR>
						<TD ALIGN="RIGHT">Peso(kg):</TD>
						<TD><input class="form-control" id="peso-imc"></TD>
	                </TR>
	                <TR>
						<TD ALIGN="RIGHT">IMC:</TD>
						<TD>
							<input class="form-control" id="result-imc">
							<button type="button" class="btn btn-info" id="btn-calc-imc">Calcular</button>
						</TD>
                	</TR>
	        	</TABLE>
	        </div>
	        <div class="panel-footer"></div>
	    </div>
	</div>
</div>
