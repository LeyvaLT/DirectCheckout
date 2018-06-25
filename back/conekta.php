<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 25/06/18
 * Time: 02:29 PM
 */
require_once("../conekta-php/lib/Conekta.php");
\Conekta\Conekta::setApiKey("key_c727zEVsYvwEQqsmU6rdyg");
\Conekta\Conekta::setApiVersion("2.0.0");

$x = $_POST['nombre'];



/*##################################### - SE CREA UN CLIENTE NUEVO - #################################################*/

try {
    $customer = \Conekta\Customer::create(
        array(
            "name" => "Fulanito Pérez",
            "email" => "fulanito@conekta.com",
            "phone" => "+52181818181",
            "payment_sources" => array(
                array(
                    "type" => "card",
                    "token_id" => "tok_test_visa_4242"
                )
            )//payment_sources
        )//customer
    );
} catch (\Conekta\ProccessingError $error){
    echo $error->getMesage();
} catch (\Conekta\ParameterValidationError $error){
    echo $error->getMessage();
} catch (\Conekta\Handler $error){
    echo $error->getMessage();
}

/*####################################### - SE CREA UN PLAN DE PAGO - ################################################*/

$plan = \Conekta\Plan::create(
    array(
        "id" => "plan-mensual",
        "name" => "Plan que se cobra cada mes",
        "amount" => 51000,
        "currency" => "MXN",
        "interval" => "month"
    )//plan
);

/*######################################### - SE EFECTUA LA SUSCRIPCION - ############################################*/

$subscription = $customer->createSubscription(
    array(
        'plan' => 'plan-mensual'
    )
);



/*############################################# - RESPUESTA DEL WEBHOOK - ############################################*/

$body = @file_get_contents('php://input');
$data = json_decode($body);
http_response_code(200); // Return 200 OK


/*############################################## - COMPROBACION DEL PAGO - ###########################################*/

if ($data->type == 'charge.paid'){
    $payment_method = $data->charges->data->object->payment_method->type;
    $msg = "Tu pago ha sido comprobado.";
    mail("<a href='mailto:edgar@girasolo.com'>edgar@girasolo.com</a>","Pago ". $payment_method .
        " confirmado",$msg);
}



/*############################################## - PAUSAR SUSCRIPCION - ##############################################*/
/*
$customer = \Conekta\Customer::find("cus_zzmjKsnM9oacyCwV3");
$subscription = $customer->subscription->pause();
*/
/*############################################## - CANCELAR SUSCRIPCION - ############################################*/
/*
$customer = \Conekta\Customer::find("cus_zzmjKsnM9oacyCwV3");
$subscription = $customer->subscription->cancel();
*/
/*############################################## - ACTUALIZAR DEL PAGO - ###########################################*/
/*
$customer = \Conekta\Customer::find("cus_zzmjKsnM9oacyCwV3");
$subscription = $customer->subscription->update(
    array(
        "plan" => "plan-anual"
    )
);
*/

