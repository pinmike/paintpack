<?php
	// Imports
	require_once("pure360/PaintSystemException.php");
	require_once("pure360/PaintSecurityException.php");
	require_once("pure360/PaintValidationException.php");
	require_once("pure360/PaintMethods.php");

	// Receive data posted from the form
	$processInd		= (!empty($_REQUEST["processInd"])? $_REQUEST["processInd"]: "N");
	
	$messageName	= (!empty($_REQUEST["messageName"])? $_REQUEST["messageName"]: null);		
	$messageId		= (!empty($_REQUEST["messageId"])? $_REQUEST["messageId"]: null);		
	$output			= "";
	$searchOutput	= "";
	$eventData		= "";
	
	// Send the request to process
	if($processInd=="Y")
	{		
        try
        {
	      	$output			= "";
        	
            // ***** Log in and create a context *****
            $paint = new PaintMethods();
            $paint->login();

            // ***** Retrieve the event data *****
            //$searchResult = $paint->searchMessages($messageName);
            $searchResult = $paint->searchMessages($messageName, $messageId);


			// Output the meta data as a readable string
			foreach($searchResult as $searchResultItem)
			{
				$searchOutput.= print_r($searchResultItem,true)."\n\n";
			}
						
            // Output to help the user see what's going on.
            $output = "Matching message(s) found (see below)<BR/><BR/>";     
            
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
		
	} else
	{
       	// Ignore
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
	    <input type="hidden" name="processInd" value="Y" />
	    <div>
	        <a href="index.htm"><b>home</b></a><br />
	        <br />
			Search messages with a message name parameter (optional)
	        <br />
	        <font color="red"><?php echo $output; ?></font>
			Message name (optional):<br />
	        <input name="messageName" value="<?php echo $messageName; ?>" size="50"/><br />
	        <br />
	        <br />
			Message ID (optional):<br />
	        <input name="messageID" value="<?php echo $messageID; ?>" size="50"/><br />
	        <br />
	        <br />
	        Result:<br />
	        <em>(the returned messages will be displayed below)</em><br />
	        <br/>
	        <b>Messages returned:</b>
	        <br/>
	        <br/>
	        <?php echo $searchOutput;?>
			<br/>
			<br/>
	        <input type="submit" value="Search messages" /></div>
	    </div>
    </form>
</body>
</html>
