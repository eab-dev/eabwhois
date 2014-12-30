<?php /* #?ini charset="utf-8"?

[ezjscServer]
# List of permission functions as used by the eZ Publish permission system
FunctionList[]=ezjscwhois

# Settings for setting up a server function
# Url to test this server function(return node list):
# <root>/ezjscore/call/ezjscwhois::info::eab.co.uk
[ezjscServer_ezjscwhois]
Class=ezjscServerFunctionsWhois
File=extension/eabwhois/classes/ezjscserverfunctionswhois.php
Functions[]=ezjscwhois

*/ ?>