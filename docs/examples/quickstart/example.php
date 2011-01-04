<?php
/**
 * PAMI basic use example.
 *
 * PHP Version 5
 *
 * @category Pami
 * @author   Marcelo Gornstein <marcelog@gmail.com>
 * @license  http://www.noneyet.ar/ Apache License 2.0
 * @version  SVN: $Id$
 * @link     http://www.noneyet.ar/
 */
if ($argc != 5) {
    echo "Use: $argv[0] <host> <port> <user> <pass>";
    exit (254);
}

// Setup include path.
ini_set(
    'include_path',
    implode(
        PATH_SEPARATOR,
        array(
            ini_get('include_path'),
            implode(DIRECTORY_SEPARATOR, array('..', '..', '..', 'src', 'mg'))
        )
    )
);

////////////////////////////////////////////////////////////////////////////////
// Mandatory stuff to bootstrap.
////////////////////////////////////////////////////////////////////////////////
require_once 'PAMI/Autoloader/Autoloader.php'; // Include ding autoloader.
Autoloader::register(); // Call autoloader register for ding autoloader.
use PAMI\Client\Impl\ClientImpl;
use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use PAMI\Message\Action\ListCommandsAction;
use PAMI\Message\Action\ListCategoriesAction;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\CoreSettingsAction;
use PAMI\Message\Action\CoreStatusAction;
use PAMI\Message\Action\SIPPeersAction;
use PAMI\Message\Action\SIPShowRegistryAction;
use PAMI\Message\Action\StatusAction;
use PAMI\Message\Action\ReloadAction;
use PAMI\Message\Action\CommandAction;
use PAMI\Message\Action\HangupAction;
use PAMI\Message\Action\LogoffAction;
use PAMI\Message\Action\AbsoluteTimeoutAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\BridgeAction;
use PAMI\Message\Action\CreateConfigAction;
use PAMI\Message\Action\GetConfigAction;
use PAMI\Message\Action\GetConfigJSONAction;
use PAMI\Message\Action\AttendedTransferAction;
use PAMI\Message\Action\RedirectAction;
use PAMI\Message\Action\DAHDIShowChannelsAction;
use PAMI\Message\Action\DAHDIHangupAction;
use PAMI\Message\Action\DAHDIRestartAction;
use PAMI\Message\Action\DAHDIDialOffHookAction;
use PAMI\Message\Action\DAHDIDNDOnAction;
use PAMI\Message\Action\DAHDIDNDOffAction;

class A implements IEventListener
{
    public function handle(EventMessage $event)
    {
        var_dump($event);
    }
}
////////////////////////////////////////////////////////////////////////////////
// Code STARTS.
////////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
ini_set('display_errors', 1);

try
{
	$a = new ClientImpl($argv[1], $argv[2], $argv[3], $argv[4], 60, 60);
	$a->registerEventListener(new A());
	$a->open();
	var_dump($a->send(new ListCommandsAction()));
	var_dump($a->send(new CoreShowChannelsAction()));
	var_dump($a->send(new SIPPeersAction()));
	var_dump($a->send(new StatusAction()));
	var_dump($a->send(new CommandAction('sip show peers')));
	var_dump($a->send(new SIPShowRegistryAction()));
	var_dump($a->send(new CoreSettingsAction()));
	var_dump($a->send(new ListCategoriesAction('sip.conf')));
	var_dump($a->send(new CoreStatusAction()));
	var_dump($a->send(new GetConfigAction('extensions.conf')));
	var_dump($a->send(new GetConfigAction('sip.conf', 'general')));
	var_dump($a->send(new GetConfigJSONAction('extensions.conf')));
	var_dump($a->send(new DAHDIShowChannelsAction()));
	var_dump($a->send(new CreateConfigAction('foo.conf')));
	//var_dump($a->send(new DAHDIDNDOnAction('1')));
	//var_dump($a->send(new DAHDIDNDOffAction('1')));
	//var_dump($a->send(new DAHDIDialOffHookAction(1, '113')));
	//var_dump($a->send(new DAHDIRestartAction()));
	//var_dump($a->send(new RedirectAction('SIP/a-1', '51992266', 'netlabs', '1')));
	//var_dump($a->send(new AttendedTransferAction('SIP/a-1', '51992266', 'netlabs', '1')));
	//$originateMsg = new OriginateAction('SIP/marcelog');
	//$originateMsg->setContext('netlabs');
	//$originateMsg->setPriority('1');
	//$originateMsg->setExtension('51992266');
	//var_dump($a->send($originateMsg));
	//var_dump($a->send(new AbsoluteTimeoutAction('SIP/XXXX-123123', 10)));
	//var_dump($a->send(new BridgeAction('SIP/a-1', 'SIP/a-2', true)));
	//var_dump($a->send(new LogoffAction()));
	//var_dump($a->send(new HangupAction('SIP/XXXX-123123')));
	//var_dump($a->send(new DAHDIHangupAction('1')));
	//var_dump($a->send(new ReloadAction()));
	//var_dump($a->send(new ReloadAction('chan_sip')));
	$time = time();
	while((time() - $time) < 60) // Wait for events.
	{
	    $a->process();
	    usleep(1000); // 1ms delay
	}
	$a->close(); // send logoff and close the connection.
} catch (Exception $e) {
	echo $e->getMessage() . "\n";
}
////////////////////////////////////////////////////////////////////////////////
// Code ENDS.
////////////////////////////////////////////////////////////////////////////////
