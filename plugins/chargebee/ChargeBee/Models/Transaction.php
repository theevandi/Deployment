<?php

class ChargeBee_Transaction extends ChargeBee_Model
{

  protected $allowed = array('id', 'customerId', 'subscriptionId', 'gatewayAccountId', 'paymentSourceId', 'paymentMethod',
'referenceNumber', 'gateway', 'type', 'date', 'settledAt', 'currencyCode', 'amount', 'idAtGateway','status', 'fraudFlag', 'errorCode', 'errorText', 'voidedAt', 'resourceVersion', 'updatedAt','fraudReason', 'amountUnused', 'maskedCardNumber', 'referenceTransactionId', 'refundedTxnId','reversalTransactionId', 'linkedInvoices', 'linkedCreditNotes', 'linkedRefunds', 'deleted');



  # OPERATIONS
  #-----------

  public static function all($params = array(), $env = null, $headers = array())
  {
    return ChargeBee_Request::sendListRequest(ChargeBee_Request::GET, ChargeBee_Util::encodeURIPath("transactions"), $params, $env, $headers);
  }

  public static function transactionsForCustomer($id, $params = array(), $env = null, $headers = array())
  {
    return ChargeBee_Request::send(ChargeBee_Request::GET, ChargeBee_Util::encodeURIPath("customers",$id,"transactions"), $params, $env, $headers);
  }

  public static function transactionsForSubscription($id, $params = array(), $env = null, $headers = array())
  {
    return ChargeBee_Request::send(ChargeBee_Request::GET, ChargeBee_Util::encodeURIPath("subscriptions",$id,"transactions"), $params, $env, $headers);
  }

  public static function paymentsForInvoice($id, $params = array(), $env = null, $headers = array())
  {
    return ChargeBee_Request::send(ChargeBee_Request::GET, ChargeBee_Util::encodeURIPath("invoices",$id,"payments"), $params, $env, $headers);
  }

  public static function retrieve($id, $env = null, $headers = array())
  {
    return ChargeBee_Request::send(ChargeBee_Request::GET, ChargeBee_Util::encodeURIPath("transactions",$id), array(), $env, $headers);
  }

 }

?>