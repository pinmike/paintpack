<?php
	// Imports
	require_once("pure360/PaintSystemException.php");
	require_once("pure360/PaintSecurityException.php");
	require_once("pure360/PaintValidationException.php");
	require_once("pure360/PaintMethods.php");

	// Receive data posted from the form
	$messageId 	= (!empty($_REQUEST["messageId"])? $_REQUEST["messageId"]: null);
	$output			= "";
	$deliveryData	= "";
	
	// Send the request to process
	if(!empty($messageId))
	{		
	    $paint = new PaintMethods();
	     
        try
        {
        	$messageOutput = null;
        	$displayFields	= array("messageName");
        	
            // ***** Log in and create a context *****
            $paint->login();

            // ***** Load the delivery record *****
            $messageOutput = $paint->loadMessage($messageId);

            // Output to help the user see what's going on.
            $output = "Message found.  See below for details:<BR/><BR/>";
            
            // Remove some of the less interesting data from the array and then output the rest
            foreach($messageOutput as $fieldName=>$fieldValue)
            {
            	if(in_array($fieldName, $displayFields))
            	{
		            $messageData .= $fieldName." = ".$fieldValue."\n";
		        }
	        }
        }
        catch (PaintValidationException $pve)
        {
            $output = "Validation Error<BR/><BR/>".
                                    $paint->convertResultToDebugString($pve->getErrors())."<BR/><BR/>";
        }
        catch (PaintSecurityException $psece)
        {
            $output = "Security Exception<BR/><BR/>".$psece->getMessage()."<BR/><BR/>";
        }
        catch (PaintSystemException $pse)
        {
            $output = "System Exception<BR/><BR/>".$pse->getMessage()."<BR/><BR/>";
        }
        catch (Exception $exp)
        {
            $output = "Unhandled Exception<BR/><BR/>".$exp->getMessage()."<BR/><BR/>";
        }

        // Log out of the session.  This should be placed so that
        // it will always occur even if there is an exception
        try
        {
            $paint->logout();
        }
        catch (Exception $exp)
        {
        	// Ignore
        }				
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>))) Pure: PAINT Example Implementation</title>    
    <link rel="stylesheet" type="text/css" href="paint.css" />    
</head>
<body>
    <form action="" method="post">
    <div>
        <a href="index.htm"><b>home</b></a><br />
        <br />
        Load an existing message by ID.&nbsp; You will need the reference number (message id) that was 
        returned when the message was created.<br />
        <br />
        <font color="red"><?php echo $output; ?></font>Message reference (id):
        <input name="messageId" value="<?php echo $messageId; ?>"/>
        <input type="submit" value="Load message" />
		<pre><?php echo $messageData; ?></pre>
    </div>
    </form>
</body>
</html>
