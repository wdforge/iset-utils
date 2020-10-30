<?php

namespace Iset\Utils;


/**
 * Класс хэндлер, нужен для перехвата и последующей обработки ошибок на стороне системы.
 */
class ErrorHandler
{
  /**
   * Формирование записи ошибки в лог.
   *
   * @param integer $errno номер ошибки
   * @param string $errstr сообщение об ошибке
   * @param string $errfile фаил
   * @param string $errline
   *
   * @return true | none
   */
  public function stringifyErrorType($type)
  {
    if (is_object($type)) {
      return 'exception';
    }
    switch ($type) {
      case E_ERROR: // 1 //
        return 'FATAL';
      case E_WARNING: // 2 //
        return 'E_WARNING';
      case E_PARSE: // 4 //
        return 'E_PARSE';
      case E_NOTICE: // 8 //
        return 'E_NOTICE';
      case E_CORE_ERROR: // 16 //
        return 'E_CORE_ERROR';
      case E_CORE_WARNING: // 32 //
        return 'E_CORE_WARNING';
      case E_COMPILE_ERROR: // 64 //
        return 'E_COMPILE_ERROR';
      case E_COMPILE_WARNING: // 128 //
        return 'E_COMPILE_WARNING';
      case E_USER_ERROR: // 256 //
        return 'E_USER_ERROR';
      case E_USER_WARNING: // 512 //
        return 'E_USER_WARNING';
      case E_USER_NOTICE: // 1024 //
        return 'E_USER_NOTICE';
      case E_STRICT: // 2048 //
        return 'E_STRICT';
      case E_RECOVERABLE_ERROR: // 4096 //
        return 'E_RECOVERABLE_ERROR';
      case E_DEPRECATED: // 8192 //
        return 'E_DEPRECATED';
      case E_USER_DEPRECATED: // 16384 //
        return 'E_USER_DEPRECATED';
    }
    return $type;
  }

  public function error_handler($errno = 0, $errstr = '', $errfile = '', $errline = '')
  {
    $message = "";
    $criticalError = false;
    if (!is_object($errno)) {
      switch ($errno) {
        case E_ERROR:
        case E_COMPILE_ERROR:
        case E_CORE_ERROR:
        case E_PARSE:
        case E_USER_ERROR:
        case E_WARNING:
          if ($errstr == "(SQL)") {
            // handling an sql error
            $message .= "<b>SQL Error</b> [$errno] " . SQLMESSAGE . "<br />\n";
            $message .= "Query : " . SQLQUERY . "<br />\n";
            $message .= "On line " . SQLERRORLINE . " in file " . SQLERRORFILE . " ";
            $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            $message .= "Aborting...<br />\n";
          } else {
            $message .= "<b>" . $this->stringifyErrorType($errno) . "</b> [$errno] $errstr<br />\n";
            $message .= ($errno == E_WARNING ? ' Warning' : ' Fatal error') . "  on line $errline in file $errfile";
            $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            $message .= "Aborting...<br />\n";
          }

          $criticalError = true;
          break;
        default :
        {
          $message .= "<b>" . $this->stringifyErrorType($errno) . "</b> [$errno] $errstr<br />\n";
          $message .= "  warning on line $errline in file $errfile";
          $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
          break;
        }
      }
    } else {
      /* @var \Exception $errno */
      $message .= "<b> Uncatched Exception </b>  " . $errno->getMessage() . "<br />\n";
      $message .= "  warning on line " . $errno->getLine() . " in file " . $errno->getFile();
      $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
      $criticalError = true;
    }

    try {
      if (is_object($errno)) {
        /* @var \Exception $errno */
        throw $errno;
      } else {
        throw new \Exception('');
      }
    } catch (\Exception $e) {
      $trace = explode("\n", $e->getTraceAsString());
      array_shift($trace);

      $message .= "\n" . implode("<BR>\n", $trace);
    }

    Logger::log($message);

    if ($criticalError) {
      if (ini_get('display_errors')) {
        echo $message;
      } else {
        echo 'critical error';
      }
      die();
    }

    if (ini_get('display_errors'))
      echo $message;

    return true;
  }

  public function shutdown_handler()
  {
    $error = error_get_last();
    if (empty($error) || $error['type'] != E_ERROR) {
      return;
    }
    $this->error_handler(E_ERROR, $error['message'], $error['file'], $error['line']);
  }


  public function error_exception($errno, $errstr, $errfile, $errline, $context)
  {
    $this->error_handler($errno, $errstr, $errfile, $errline);
  }

  public function init()
  {

    set_error_handler([$this, 'error_handler']);
    set_exception_handler([$this, 'error_handler']);
    register_shutdown_function([$this, 'shutdown_handler']);
  }

}
