<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use Auth;
use DateTime;
use App\Http\Controllers\PagSeguro\Config;
use App\Http\Controllers\PagSeguro\Transporter;  
use App\Http\Controllers\PagSeguro\Document;
use App\Http\Controllers\PagSeguro\Phone;
use App\Http\Controllers\PagSeguro\Address;
use App\Http\Controllers\PagSeguro\Sender;
use App\Http\Controllers\PagSeguro\Shipping;
use App\Http\Controllers\PagSeguro\CreditCard;
use App\Http\Controllers\PagSeguro\Item;
use App\Http\Controllers\PagSeguro\Payment;
use App\Http\Controllers\PagSeguro\CreditCard\Installment;
use App\Http\Controllers\PagSeguro\CreditCard\Holder;
use App\Pedido;

class PagamentoController extends Controller
{	
	public function pagamento(){
		 
	}

	public function escolherFormaPagamento(){
		//Se estiver logado exibir pagamento
        if(Auth::guard('painelfinger')->check()){ 
        	$order = "";
        	$msgError = "";
        	$years = [];
            for($y = date('Y'); $y < date('Y')+14; $y++){
                 array_push($years,$y);
            }
        	$pagsegurourlJS = Config::getUrlJS(); 
        	$pagseguroid = Transporter::createSession(); 
            $pagseguromaxInstallmentNoInterest = Config::MAX_INSTALLMENT_NO_INTEREST;
            $pagseguromaxInstallment = Config::MAX_INSTALLMENT;
        	return view('payment',compact('order','msgError','years','pagsegurourlJS','pagseguroid','pagseguromaxInstallment','pagseguromaxInstallmentNoInterest'));
        }else{
            return 'Não logado';
        }
	}

    public function pagamentoCredito(Request $request){
        
        //Recuperar dados do carrinho
        //Se estiver logado exibir pagamento
        if(Auth::guard('painelfinger')->check()){     
            $user = Auth::guard('painelfinger')->user();
            $cliente = $user->cliente()->first();
            /*$cpf = new Document(Document::CPF, $request->cpf);            
            $phone = new Phone($request->ddd,$request->phone);
            $address = new Address(
                "Rua Teste",
                "140", 
                "complemento",
                "Bairro exemplo",
                "18078-666",
                "Sorocaba",
                "SP",
                "Brasil"
            );  
            $birthDate = new DateTime($request->birth);
            $sender = new Sender('Fulano da Silva',$cpf,$birthDate,$phone, 'teste@sandbox.pagseguro.com.br', $request->hash);             
            $holder = new Holder('fulano da silva',$cpf,$birthDate,$phone);
            //Dados de entrega $cart->getvlfreight() valor frete
            $shipping = new Shipping($address, (float)"0.00", Shipping::PAC);
            $valor_installment = number_format((float)$request->installments_value,2,'.',''); 
            $installment = new Installment((int)$request->installments_qtd,$valor_installment);  
            //Endereço de fatura
            $billingAddress = new Address(
                "Rua Teste",
                "140", 
                "complemento",
                "Bairro",
                "18078666",
                "Sorocaba",
                "SP",
                "Brasil" 
            );
            $creditCard = new CreditCard(
                $request->token,
                $installment,
                $holder,
                $billingAddress 
            );  
            $payment = new Payment(
                //$order->getidorder(),
                1,
                $sender,
                $shipping
            );
            //VARIOS ITENS DO CARRINHO
            /*
            foreach ($cart->getProducts() as $product) {
                $item = new Item(
                    (int)$product['idproduct'],
                    $product['desproduct'],
                    (float)$product['vlprice'],
                    (int)$product['nrqtd']
                );  
                $payment->addItem($item);
            } */
            //Carregar um item
            /*$item = new Item(
                1,
                'celular motorola',
                120.00,
                1
            );  
            $payment->addItem($item);
            $payment->setCreditCard($creditCard); 
            $dom = $payment->getDOMDocument();  
            //echo $dom->saveXml();
            //exit();   
            Transporter::sendTransaction($payment);
            echo json_encode([
                'success'=>true
            ]);*/
            //if($this->sandbox){
            $dados = $request->all();
            $dados['emailsender']= 'teste@sandbox.pagseguro.com.br';//E-mail comprador sandbox
            //}else{
            //    $dados['emailsender']= $userfinal['email'];//E-mail do cliente comprador
            //}

            $dados['quantidade_parcelas']= json_decode($dados['installments_qtd'], true);
            $valor_parcela = $dados['quantidade_parcelas']['installments_value'];
            $valor_total = 500.00;//Pega o valor total do input hide
            //$cpf_titular= str_replace(array('.','-'), array('',''), $dados['cpf']);

            //$id_last_pedido= Pedido::all()->last();//Recupera o ID do ultimo pedido
            //Verifica se existe pedido se não existir deixa como pedido numero 1
            /*if($id_last_pedido) 
            {
                $id_last_pedido= $id_last_pedido->id + 1;
            }else{
                $id_last_pedido= 1;
            }*/
            $data['token'] = Config::SANDBOX_TOKEN; //token sandbox ou produção 
            $data['paymentMode'] = 'default';
            $data['creditCardToken'] = $dados['token']; //gerado via javascript
            $data['paymentMethod'] = 'creditCard';
            $data['receiverEmail'] = Config::SANDBOX_EMAIL;
            $data['senderHash'] = $dados['hash']; //gerado via javascript
            $data['senderName'] = 'Fulano da Silva'; //nome do usuário deve conter nome e sobrenome
            $data['senderAreaCode'] = $dados['ddd'];
            $data['senderPhone'] = $dados['phone'];
            $data['senderEmail'] = 'teste@sandbox.pagseguro.com.br'; 
            $data['senderCPF'] = $dados['cpf'];    

            $data['installmentQuantity'] = 1;//Quantidade de parcelas escolhida pelo cliente
            $data['noInterestInstallmentQuantity'] = Config::MAX_INSTALLMENT_NO_INTEREST;
            $data['installmentValue'] = number_format((float)$request->installments_value,2,'.',''); //valor da parcela
            
            $data['creditCardHolderName'] = 'Fulano da Silva'; //nome do titular
            $data['creditCardHolderCPF'] = $dados['cpf'];
            $birthDate = new DateTime($request->birth);
            $data['creditCardHolderBirthDate'] = $birthDate;
            $data['creditCardHolderAreaCode'] = $dados['ddd'];
            $data['creditCardHolderPhone'] = $dados['phone'];
              
            $data['billingAddressStreet'] = "Rua Teste";
            $data['billingAddressNumber'] = "140";
            $data['billingAddressDistrict'] = "Bairro"; 
            $data['billingAddressPostalCode'] = "18078666";
            $data['billingAddressCity'] = "Sorocaba";
            $data['billingAddressState'] = "SP";
            $data['billingAddressCountry'] = 'Brasil';   

            $data['currency'] = 'BRL';
            $data['itemId1'] = 1;
            $data['itemQuantity1'] = '1';
            $data['itemDescription1'] = 'celular motorola';
            $data['reference'] = 'Pedido nº # 1'; //referencia qualquer do produto
            $data['shippingAddressRequired'] = 'false';
            $data['itemAmount1'] = number_format(500,2,".","");
            $data = http_build_query($data);
            //$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions'; //URL de teste 
            $curl = curl_init();   
            $headers = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'); 
            curl_setopt($curl, CURLOPT_URL, Config::SANDBOX_URL_TRANSACTION."?".http_build_query(Config::getAuthentication()));            
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl,CURLOPT_HTTPHEADER, $headers );
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true );
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            //curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $xml = curl_exec($curl);
            curl_close($curl);
             
            $retono= simplexml_load_string($xml);
            $retorno= json_encode($retono);
            $retorno = json_decode($retorno,TRUE); 
            
            if( !empty($retorno['code']) ){ 
                $usuario= Auth::guard('painelfinger')->user();//User logado 
                //Array com os tipos de pagamentos do Pagseguro
                $tipo_pg['101']= 'Cartão de crédito Visa';
                $tipo_pg['102']= 'Cartão de crédito MasterCard';
                $tipo_pg['103']= 'Cartão de crédito American Express';
                $tipo_pg['104']= 'Cartão de crédito Diners';
                $tipo_pg['105']= 'Cartão de crédito Hipercard';
                $tipo_pg['106']= 'Cartão de crédito Aura';
                $tipo_pg['107']= 'Cartão de crédito Elo';
                $tipo_pg['108']= 'Cartão de crédito PLENOCard';
                $tipo_pg['109']= 'Cartão de crédito PersonalCard';
                $tipo_pg['110']= 'Cartão de crédito JCB';
                $tipo_pg['111']= 'Cartão de crédito Discover';
                $tipo_pg['112']= 'Cartão de crédito BrasilCard';
                $tipo_pg['113']= 'Cartão de crédito FORTBRASIL';
                $tipo_pg['114']= 'Cartão de crédito CARDBAN';
                $tipo_pg['115']= 'Cartão de crédito VALECARD';
                $tipo_pg['116']= 'Cartão de crédito Cabal';
                $tipo_pg['117']= 'Cartão de crédito Mais!';
                $tipo_pg['118']= 'Cartão de crédito Avista';
                $tipo_pg['119']= 'Cartão de crédito GRANDCARD';
                $tipo_pg['120']= 'Cartão de crédito Sorocred';
                $tipo_pg['201']= 'Boleto Bradesco';
                $tipo_pg['202']= 'Boleto Santander';
                $tipo_pg['301']= 'Débito online Bradesco';
                $tipo_pg['302']= 'Débito online Itaú';
                $tipo_pg['303']= 'Débito online Unibanco';
                $tipo_pg['304']= 'Débito online Banco do Brasil';
                $tipo_pg['305']= 'Débito online Banco Real';
                $tipo_pg['306']= 'Débito online Banrisul';
                $tipo_pg['307']= 'Débito online HSBC';
                $tipo_pg['401']= 'Saldo PagSeguro';
                $tipo_pg['501']= 'Oi Paggo';
                $tipo_pg['701']= 'Depósito em conta - Banco do Brasil';
                $tipo_pg['702']= 'Depósito em conta - HSBC';
                
                //Gravando o pedido
                $pedido['pagseg_transacao']= $retorno['code'];
                $pedido['meio_pg']= $tipo_pg[$retorno['paymentMethod']['code']];
                $pedido['valor_compra']= $retorno['grossAmount'];
                $pedido['status_pg']= $retorno['status'];
                $pedido['cliente_id']= $cliente->id;   
                $pedido['user_id'] = $user->id;  
                $pedido_gravado = Pedido::create($pedido); 
                //Fim Gravando o pedido
                
                /*
                $carrinho = session()->get('carrinho'); // Second argument is a default value
                /* 
                foreach ($carrinho as $item) {
                     
                    $dados_servicos['anuncio_id'] = $item['anuncio_id'];
                    $dados_servicos['pedido_id'] = $pedido_gravado->id;
                    $dados_servicos['opcao_id'] = $item['opcao'];

                    if(isset($item['opcaoOn'])){
                        $dados_servicos['opcao_detalhe'] = $item['opcaoOn']; 
                    }else if(isset($item['opcaoPre'])){
                        $dados_servicos['opcao_detalhe'] = $item['opcaoPre'];
                    }

                    $dados_servicos['cliente_id'] = $cliente->id;  
                    $dados_servicos['itemValor'] = $item['itemValor'];  
      
                    $pedidoservico = PedidosServico::create($dados_servicos); 
                     
                    if(isset($item['horario'])){
                        foreach ($item['horario'] as $agenda) {
                            $data_hora= explode('|', $agenda);
                            $dados_agenda['data']= $data_hora[0];
                            $dados_agenda['hora']= $data_hora[1];
                            $dados_agenda['pedido_id']= $pedido_gravado->id;
                            $dados_agenda['pedido_servico_id'] = $pedidoservico->id;  
                            PedidosAgenda::create($dados_agenda);  
                        }  
                    } 
                }   
                $request->session()->forget('carrinho'); 
                Session::flash('message', 'Pedido realizado com sucesso!');
                Session::flash('class', 'success'); */   
                //return redirect()->route('painel.meuspedidos'); */  
                return json_encode(['success'=>true]); 
            }else{      
                $msgError = 'Pagamento não foi realizado'; 
                return redirect()->back(); 
            } 
        }
    }

    public function pagamentoBoleto(Request $request){
        
        //Recuperar dados do carrinho
        //Se estiver logado exibir pagamento
        if(Auth::guard('painelfinger')->check()){     
            $dados = $request->all(); 
            $user = Auth::guard('painelfinger')->user();
            $cliente = $user->cliente()->first();
            $dados = $request->all();
            $dados['emailsender']= 'teste@sandbox.pagseguro.com.br';//E-mail comprador sandbox  
            $data['paymentMode'] = 'default'; 
            $data['paymentMethod'] = 'boleto';
            $data['receiverEmail'] = Config::SANDBOX_EMAIL;
            $data['senderHash'] = $dados['hash']; //gerado via javascript
            $data['senderName'] = 'Fulano da Silva'; //nome do usuário deve conter nome e sobrenome
            $data['senderAreaCode'] = $dados['ddd'];
            $data['senderPhone'] = $dados['phone'];
            $data['senderEmail'] = 'teste@sandbox.pagseguro.com.br'; 
            $data['senderCPF'] = $dados['cpf'];    
 
            $data['currency'] = 'BRL';
            $data['itemId1'] = 1;
            $data['itemQuantity1'] = '1';
            $data['itemDescription1'] = 'celular motorola';
            $data['reference'] = 'Pedido nº # 1'; //referencia qualquer do produto
            $data['shippingAddressRequired'] = 'false';
            $data['itemAmount1'] = number_format(500,2,".","");
            $data = http_build_query($data);
            //$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/transactions'; //URL de teste 
            $curl = curl_init();   
            $headers = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'); 
            curl_setopt($curl, CURLOPT_URL, Config::SANDBOX_URL_TRANSACTION."?".http_build_query(Config::getAuthentication()));            
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl,CURLOPT_HTTPHEADER, $headers );
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true );
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            //curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $xml = curl_exec($curl);
            curl_close($curl);
             
            $retono= simplexml_load_string($xml);
            $retorno= json_encode($retono);
            $retorno = json_decode($retorno,TRUE); 
             
            if( !empty($retorno['code']) ){ 
                $usuario= Auth::guard('painelfinger')->user();//User logado 
                //Array com os tipos de pagamentos do Pagseguro
                $tipo_pg['101']= 'Cartão de crédito Visa';
                $tipo_pg['102']= 'Cartão de crédito MasterCard';
                $tipo_pg['103']= 'Cartão de crédito American Express';
                $tipo_pg['104']= 'Cartão de crédito Diners';
                $tipo_pg['105']= 'Cartão de crédito Hipercard';
                $tipo_pg['106']= 'Cartão de crédito Aura';
                $tipo_pg['107']= 'Cartão de crédito Elo';
                $tipo_pg['108']= 'Cartão de crédito PLENOCard';
                $tipo_pg['109']= 'Cartão de crédito PersonalCard';
                $tipo_pg['110']= 'Cartão de crédito JCB';
                $tipo_pg['111']= 'Cartão de crédito Discover';
                $tipo_pg['112']= 'Cartão de crédito BrasilCard';
                $tipo_pg['113']= 'Cartão de crédito FORTBRASIL';
                $tipo_pg['114']= 'Cartão de crédito CARDBAN';
                $tipo_pg['115']= 'Cartão de crédito VALECARD';
                $tipo_pg['116']= 'Cartão de crédito Cabal';
                $tipo_pg['117']= 'Cartão de crédito Mais!';
                $tipo_pg['118']= 'Cartão de crédito Avista';
                $tipo_pg['119']= 'Cartão de crédito GRANDCARD';
                $tipo_pg['120']= 'Cartão de crédito Sorocred';
                $tipo_pg['201']= 'Boleto Bradesco';
                $tipo_pg['202']= 'Boleto Santander';
                $tipo_pg['301']= 'Débito online Bradesco';
                $tipo_pg['302']= 'Débito online Itaú';
                $tipo_pg['303']= 'Débito online Unibanco';
                $tipo_pg['304']= 'Débito online Banco do Brasil';
                $tipo_pg['305']= 'Débito online Banco Real';
                $tipo_pg['306']= 'Débito online Banrisul';
                $tipo_pg['307']= 'Débito online HSBC';
                $tipo_pg['401']= 'Saldo PagSeguro';
                $tipo_pg['501']= 'Oi Paggo';
                $tipo_pg['701']= 'Depósito em conta - Banco do Brasil';
                $tipo_pg['702']= 'Depósito em conta - HSBC';
                
                //Gravando o pedido
                $pedido['pagseg_transacao']= $retorno['code'];
                $pedido['meio_pg']= $tipo_pg[$retorno['paymentMethod']['code']];
                $pedido['valor_compra']= $retorno['grossAmount'];
                $pedido['status_pg']= $retorno['status'];
                $pedido['paymentLink']= $retorno['paymentLink'];  
                $pedido['cliente_id']= $cliente->id;    
                $pedido['user_id'] = $user->id;   
                $pedido_gravado = Pedido::create($pedido); 
                //Fim Gravando o pedido
                 
                $request->session()->forget('carrinho'); 
                Session::flash('message', 'Pedido realizado com sucesso!');
                Session::flash('class', 'success');     
                //return redirect()->route('painel.meuspedidos'); */  
                return json_encode(['success'=>true]); 
            }else{      
                $msgError = 'Pagamento não foi realizado'; 
                return redirect()->back(); 
            } 
        }
    }


    public function pagamentoDebito(Request $request){
        
        //Recuperar dados do carrinho 
        //Se estiver logado exibir pagamento
        if(Auth::guard('painelfinger')->check()){     
            $user = Auth::guard('painelfinger')->user();
            
            $cliente = $user->cliente()->first();             
            $dados = $request->all();             
            $dados['emailsender']= 'teste@sandbox.pagseguro.com.br';//E-mail comprador sandbox             
            $data['paymentMode'] = 'default';
            $data['paymentMethod'] = 'eft';
            $data['bankName'] = $dados['bank'];
            $data['currency'] = 'BRL';
            $data['receiverEmail'] = Config::SANDBOX_EMAIL;
            $data['senderHash'] = $dados['hash']; //gerado via javascript
            $data['senderName'] = 'Fulano da Silva'; //nome do usuário deve conter nome e sobrenome
            $data['senderAreaCode'] = $dados['ddd'];
            $data['senderPhone'] = $dados['phone'];
            $data['senderEmail'] = 'teste@sandbox.pagseguro.com.br'; 
            $data['senderCPF'] = $dados['cpf'];    

            $data['installmentQuantity'] = 1;//Quantidade de parcelas escolhida pelo cliente
            $data['noInterestInstallmentQuantity'] = Config::MAX_INSTALLMENT_NO_INTEREST;
            $data['installmentValue'] = number_format((float)$request->installments_value,2,'.',''); //valor da parcela
            
            $data['creditCardHolderName'] = 'Fulano da Silva'; //nome do titular
            $data['creditCardHolderCPF'] = $dados['cpf'];
            $birthDate = new DateTime($request->birth);
            $data['creditCardHolderBirthDate'] = $birthDate;
            $data['creditCardHolderAreaCode'] = $dados['ddd'];
            $data['creditCardHolderPhone'] = $dados['phone'];
              
            $data['billingAddressStreet'] = "Rua Teste";
            $data['billingAddressNumber'] = "140";
            $data['billingAddressDistrict'] = "Bairro"; 
            $data['billingAddressPostalCode'] = "18078666";
            $data['billingAddressCity'] = "Sorocaba";
            $data['billingAddressState'] = "SP";
            $data['billingAddressCountry'] = 'Brasil';   

            $data['currency'] = 'BRL';
            $data['itemId1'] = 1;
            $data['itemQuantity1'] = '1';
            $data['itemDescription1'] = 'celular motorola';
            $data['reference'] = 'Pedido nº # 1'; //referencia qualquer do produto
            $data['shippingAddressRequired'] = 'false';
            $data['itemAmount1'] = number_format(500,2,".","");
            $data = http_build_query($data);

            $curl = curl_init();   
            $headers = array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'); 
            curl_setopt($curl, CURLOPT_URL, Config::SANDBOX_URL_TRANSACTION."?".http_build_query(Config::getAuthentication()));            
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl,CURLOPT_HTTPHEADER, $headers );
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true );
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            //curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $xml = curl_exec($curl);
            curl_close($curl);
             
            $retono= simplexml_load_string($xml);
            $retorno= json_encode($retono);
            $retorno = json_decode($retorno,TRUE); 
            
            if( !empty($retorno['code']) ){ 
                $usuario= Auth::guard('painelfinger')->user();//User logado 
                //Array com os tipos de pagamentos do Pagseguro
                $tipo_pg['101']= 'Cartão de crédito Visa';
                $tipo_pg['102']= 'Cartão de crédito MasterCard';
                $tipo_pg['103']= 'Cartão de crédito American Express';
                $tipo_pg['104']= 'Cartão de crédito Diners';
                $tipo_pg['105']= 'Cartão de crédito Hipercard';
                $tipo_pg['106']= 'Cartão de crédito Aura';
                $tipo_pg['107']= 'Cartão de crédito Elo';
                $tipo_pg['108']= 'Cartão de crédito PLENOCard';
                $tipo_pg['109']= 'Cartão de crédito PersonalCard';
                $tipo_pg['110']= 'Cartão de crédito JCB';
                $tipo_pg['111']= 'Cartão de crédito Discover';
                $tipo_pg['112']= 'Cartão de crédito BrasilCard';
                $tipo_pg['113']= 'Cartão de crédito FORTBRASIL';
                $tipo_pg['114']= 'Cartão de crédito CARDBAN';
                $tipo_pg['115']= 'Cartão de crédito VALECARD';
                $tipo_pg['116']= 'Cartão de crédito Cabal';
                $tipo_pg['117']= 'Cartão de crédito Mais!';
                $tipo_pg['118']= 'Cartão de crédito Avista';
                $tipo_pg['119']= 'Cartão de crédito GRANDCARD';
                $tipo_pg['120']= 'Cartão de crédito Sorocred';
                $tipo_pg['201']= 'Boleto Bradesco';
                $tipo_pg['202']= 'Boleto Santander';
                $tipo_pg['301']= 'Débito online Bradesco';
                $tipo_pg['302']= 'Débito online Itaú';
                $tipo_pg['303']= 'Débito online Unibanco';
                $tipo_pg['304']= 'Débito online Banco do Brasil';
                $tipo_pg['305']= 'Débito online Banco Real';
                $tipo_pg['306']= 'Débito online Banrisul';
                $tipo_pg['307']= 'Débito online HSBC';
                $tipo_pg['401']= 'Saldo PagSeguro';
                $tipo_pg['501']= 'Oi Paggo';
                $tipo_pg['701']= 'Depósito em conta - Banco do Brasil';
                $tipo_pg['702']= 'Depósito em conta - HSBC';
                
                //Gravando o pedido
                $pedido['pagseg_transacao']= $retorno['code'];
                $pedido['meio_pg']= $tipo_pg[$retorno['paymentMethod']['code']];
                $pedido['valor_compra']= $retorno['grossAmount'];
                $pedido['status_pg']= $retorno['status'];
                $pedido['paymentLink']= $retorno['paymentLink'];  
                $pedido['cliente_id']= $cliente->id;    
                $pedido['user_id'] = $user->id;   
                $pedido_gravado = Pedido::create($pedido); 
                //Fim Gravando o pedido
                
                /*
                $carrinho = session()->get('carrinho'); // Second argument is a default value
                /* 
                foreach ($carrinho as $item) {
                     
                    $dados_servicos['anuncio_id'] = $item['anuncio_id'];
                    $dados_servicos['pedido_id'] = $pedido_gravado->id;
                    $dados_servicos['opcao_id'] = $item['opcao'];

                    if(isset($item['opcaoOn'])){
                        $dados_servicos['opcao_detalhe'] = $item['opcaoOn']; 
                    }else if(isset($item['opcaoPre'])){
                        $dados_servicos['opcao_detalhe'] = $item['opcaoPre'];
                    }

                    $dados_servicos['cliente_id'] = $cliente->id;  
                    $dados_servicos['itemValor'] = $item['itemValor'];  
      
                    $pedidoservico = PedidosServico::create($dados_servicos); 
                     
                    if(isset($item['horario'])){
                        foreach ($item['horario'] as $agenda) {
                            $data_hora= explode('|', $agenda);
                            $dados_agenda['data']= $data_hora[0];
                            $dados_agenda['hora']= $data_hora[1];
                            $dados_agenda['pedido_id']= $pedido_gravado->id;
                            $dados_agenda['pedido_servico_id'] = $pedidoservico->id;  
                            PedidosAgenda::create($dados_agenda);  
                        }  
                    } 
                }   
                $request->session()->forget('carrinho'); 
                Session::flash('message', 'Pedido realizado com sucesso!');
                Session::flash('class', 'success'); */   
                //return redirect()->route('painel.meuspedidos'); */  
                return json_encode(['success'=>true]); 
            }else{      
                $msgError = 'Pagamento não foi realizado'; 
                return redirect()->back(); 
            } 
        }
    }


    public function notificacao(Request $request){
         
        $notificacao= $request->all();

        $code = $notificacao['notificationCode'];

        $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$code.'?email='.$this->emailvendedor_pagseg.'&token='.$this->token_pagseg;

        //dd($url); 

        $curl = curl_init( $url );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $xml= curl_exec($curl);

        curl_close($curl);

        //dd($xml);

        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $dados = json_decode($json,true);

        //dd($dados);

        if(!empty($dados['code']))
        {

            $pedido= Pedido::where('pagseg_transacao', $dados['code'])->first(); 

            $up['status_pg']= $dados['status'];

            //Caso o status de pagamento for 3 "Pago" atualizamos os pedido servicos e pedidos agenda
            if($dados['status'] == 3 ){

                $up['data_pg']= date("Y-m-d H:m:s");

                $pedidos_servicos= PedidosServico::where('pedido_id', $pedido->id)->get();

                //dd($pedidos_servicos);

                foreach ($pedidos_servicos as $servico) {
                    $up_serv['status']= 1;
                    PedidosServico::where('id',$servico->id)->update($up_serv);
                }


                $pedidos_agenda= PedidosAgenda::where('pedido_id', $pedido->id)->get();


                foreach ($pedidos_agenda as $agenda) {
                    $up_agenda['status']= 1;
                    PedidosAgenda::where('id',$agenda->id)->update($up_agenda);
                }

            }

            $pedido->update($up);
        }
        
    }

    public function notificacaoTeste(){
         
        //$notificacao= $request->all();

        //$code = $notificacao['notificationCode'];
        $code = 'F569884181FD81FD0CB2244E4FA872207610';
        $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$code.'/?'.http_build_query(Config::getAuthentication());

        //dd($url); 

        $curl = curl_init( $url );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $xml = curl_exec($curl);

        curl_close($curl);

        //dd($xml);

        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $dados = json_decode($json,true);

        //dd($dados['code']);

        if(!empty($dados['code']))
        {

            $pedido = Pedido::where('pagseg_transacao', $dados['code'])->first(); 

            $up['status_pg'] = $dados['status'];

            //Caso o status de pagamento for 3 "Pago" - fazer tratativas para quando estiver pago
            if($dados['status'] == 3 ){

                $up['data_pg']= date("Y-m-d H:m:s");
  
            }

            $pedido->update($up);
        }
        return 'Ok - Atualizado';
    }


}