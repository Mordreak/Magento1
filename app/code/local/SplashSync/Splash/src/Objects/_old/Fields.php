<?php

/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace   Splash\Local\Objects\Order;

use Splash\Models\ObjectBase;
use Splash\Core\SplashCore                          as Splash;
use Mage;

/**
 * @abstract    Splash PHP Module For Magento 1 - Order Object Intégration SubClass
 * @author      B. Paquier <contact@splashsync.com>
 */
class Fields extends ObjectBase
{
    //====================================================================//
    // Class Constructor
    //====================================================================//
        
    /**
     *      @abstract       Class Constructor (Used only if localy necessary)
     *      @return         int                     0 if KO, >0 if OK
     */
    function __construct()
    {
        //====================================================================//
        // Place Here Any SPECIFIC Initialisation Code
        //====================================================================//
        return True;
    }    
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
    *   @abstract     Return List Of available data for Customer
    *   @return       array   $data             List of all customers available data
    *                                           All data must match with OSWS Data Types
    *                                           Use OsWs_Data::Define to create data instances
    */
    public function Fields()
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);             
        //====================================================================//
        //  Load Local Translation File
        Splash::Translator()->Load("objects@local");          
        //====================================================================//
        // CORE INFORMATIONS
        //====================================================================//
        $this->buildCoreFields();
        //====================================================================//
        // MAIN INFORMATIONS
        //====================================================================//
        $this->buildMainFields();
        //====================================================================//
        // ORDER ADDRESS INFORMATIONS
        //====================================================================//
        $this->buildAddressFields();
        //====================================================================//
        // MAIN ORDER LINE INFORMATIONS
        //====================================================================//
        $this->buildProductsLineFields();
        //====================================================================//
        // META INFORMATIONS
        //====================================================================//
        $this->buildMetaFields();
        //====================================================================//
        // Publish Fields
        return $this->FieldsFactory()->Publish();
    }

    //====================================================================//
    // Fields Generation Functions
    //====================================================================//




}

?>
