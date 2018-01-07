<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SoapClient;

class SmsController extends Controller
{
  public function index()
  {
  try {

      // Подключаемся к серверу

      echo "<pre>";

      $client = new SoapClient('http://turbosms.in.ua/api/wsdl.html');
      // Можно просмотреть список доступных методов сервера
      var_dump($client->__getFunctions());

      // Данные авторизации
      $auth = [
          'login' => 'koliakibets',
          'password' => 'zhirkiller2017boss'
      ];

      // Авторизируемся на сервере
      $result = $client->Auth($auth);

      // Результат авторизации
      $response =  $result->AuthResult . PHP_EOL;

      // Получаем количество доступных кредитов
      $result = $client->GetCreditBalance();
      echo $response . $result->GetCreditBalanceResult . PHP_EOL;

      return;


      // Текст сообщения ОБЯЗАТЕЛЬНО отправлять в кодировке UTF-8
      $text = iconv('windows-1251', 'utf-8', 'Это сообщение будет доставлено на указанный номер');

      // Отправляем сообщение на один номер.
      // Подпись отправителя может содержать английские буквы и цифры. Максимальная длина - 11 символов.
      // Номер указывается в полном формате, включая плюс и код страны
      $sms = [
          'sender' => 'Rassilka',
          'destination' => '+380XXXXXXXXX',
          'text' => $text
      ];
      $result = $client->SendSMS($sms);

      // Отправляем сообщение на несколько номеров.
      // Номера разделены запятыми без пробелов.
      $sms = [
          'sender' => 'Rassilka',
          'destination' => '+380XXXXXXXX1,+380XXXXXXXX2,+380XXXXXXXX3',
          'text' => $text
      ];
      $result = $client->SendSMS($sms);

      // Выводим результат отправки.
      echo $result->SendSMSResult->ResultArray[0] . PHP_EOL;

      // ID первого сообщения
      echo $result->SendSMSResult->ResultArray[1] . PHP_EOL;

      // ID второго сообщения
      echo $result->SendSMSResult->ResultArray[2] . PHP_EOL;

      // Отправляем сообщение с WAPPush ссылкой
      // Ссылка должна включать http://
      $sms = [
          'sender' => 'Rassilka',
          'destination' => '+380XXXXXXXXX',
          'text' => $text,
          'wappush' => 'http://super-site.com'
      ];
      $result = $client->SendSMS($sms);

      // Запрашиваем статус конкретного сообщения по ID
      $sms = ['MessageId' => 'c9482a41-27d1-44f8-bd5c-d34104ca5ba9'];
      $status = $client->GetMessageStatus($sms);
      echo $status->GetMessageStatusResult . PHP_EOL;

  } catch(Exception $e) {
      echo 'Ошибка: ' . $e->getMessage() . PHP_EOL;
  }
  }
}
