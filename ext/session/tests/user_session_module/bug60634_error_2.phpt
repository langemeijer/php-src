--TEST--
Bug #60634 (Segmentation fault when trying to die() in SessionHandler::write()) - exception in write during exec
--INI--
session.save_path=
session.name=PHPSESSID
session.save_handler=files
--EXTENSIONS--
session
--FILE--
<?php

ob_start();

class MySessionHandler implements SessionHandlerInterface {
    function open($save_path, $session_name): bool {
        return true;
    }

    function close(): bool {
        echo "close: goodbye cruel world\n";
        return true;
    }

    function read($id): string|false {
        return '';
    }

    function write($id, $session_data): bool {
        echo "write: goodbye cruel world\n";
        throw new Exception;
    }

    function destroy($id): bool {
        return true;
    }

    function gc($maxlifetime): int {
        return true;
    }
}

session_set_save_handler(new MySessionHandler());
session_start();
session_write_close();
echo "um, hi\n";

?>
--EXPECTF--
write: goodbye cruel world

Fatal error: Uncaught Exception in %s
Stack trace:
#0 [internal function]: MySessionHandler->write('%s', '')
#1 %s(%d): session_write_close()
#2 {main}
  thrown in %s on line %d
