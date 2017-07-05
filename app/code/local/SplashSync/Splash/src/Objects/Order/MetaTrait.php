<?php
/*
 * Copyright (C) 2017   Splash Sync       <contact@splashsync.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

namespace Splash\Local\Objects\Order;

// Magento Namespaces
use Mage;
use Mage_Sales_Model_Order      as MageOrder;

/**
 * @abstract    Magento 1 Order Meta Fields Access
 */
trait MetaTrait {
    


    /**
    *   @abstract     Build Meta Fields using FieldFactory
    */
    private function buildMetaFields() {
       
        //====================================================================//
        // ORDER STATUS FLAGS
        //====================================================================//        
        
        //====================================================================//
        // Is Canceled
        // => There is no Diffrence Between a Draft & Canceled Order on Prestashop. 
        //      Any Non Validated Order is considered as Canceled
        $this->FieldsFactory()->Create(SPL_T_BOOL)
                ->Identifier("isCanceled")
                ->Name("Order" . " : " . "Canceled")
                ->MicroData("http://schema.org/OrderStatus","OrderCancelled")
                ->Association( "isCanceled","isValidated","isClosed")
                ->Group("Meta")
                ->ReadOnly();     
        
        //====================================================================//
        // Is Validated
        $this->FieldsFactory()->Create(SPL_T_BOOL)
                ->Identifier("isValidated")
                ->Name("Order" . " : " . "Valid")
                ->MicroData("http://schema.org/OrderStatus","OrderProcessing")
                ->Association( "isCanceled","isValidated","isClosed")
                ->Group("Meta")
                ->ReadOnly();
        
        //====================================================================//
        // Is Closed
        $this->FieldsFactory()->Create(SPL_T_BOOL)
                ->Identifier("isClosed")
                ->Name("Order" . " : " . "Closed")
                ->MicroData("http://schema.org/OrderStatus","OrderDelivered")
                ->Association( "isCanceled","isValidated","isClosed")
                ->Group("Meta")
                ->ReadOnly();

        //====================================================================//
        // Is Paid
        $this->FieldsFactory()->Create(SPL_T_BOOL)
                ->Identifier("isPaid")
                ->Name("Order" . " : " . "Paid")
                ->MicroData("http://schema.org/OrderStatus","OrderPaid")
                ->Group("Meta")
                ->ReadOnly();

        //====================================================================//
        // TRACEABILITY INFORMATIONS
        //====================================================================//        
        
        //====================================================================//
        // TMS - Last Change Date 
        $this->FieldsFactory()->Create(SPL_T_DATETIME)
                ->Identifier("updated_at")
                ->Name("Last update")
                ->MicroData("http://schema.org/DataFeedItem","dateModified")
                ->Group("Meta")
                ->ReadOnly();
        
    }   
    
    /**
     *  @abstract     Read requested Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     * 
     *  @return         none
     */
    private function getMetaFields($Key,$FieldName) {

        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {
            //====================================================================//
            // ORDER STATUS FLAGS
            //====================================================================//        
       
            case 'isCanceled':
                if ( $this->Object->getState() === MageOrder::STATE_CANCELED ) {
                    $this->Out[$FieldName]  = True;
                } else {
                    $this->Out[$FieldName]  = False;
                }
                break;
            case 'isValidated':
                if (    $this->Object->getState() === MageOrder::STATE_NEW 
                    ||  $this->Object->getState() === MageOrder::STATE_PROCESSING 
                    ||  $this->Object->getState() === MageOrder::STATE_COMPLETE 
                    ||  $this->Object->getState() === MageOrder::STATE_CLOSED 
                    ||  $this->Object->getState() === MageOrder::STATE_CANCELED 
                    ||  $this->Object->getState() === MageOrder::STATE_HOLDED 
                        ) {
                    $this->Out[$FieldName]  = True;
                } else {
                    $this->Out[$FieldName]  = False;
                }
                break;
            case 'isClosed':
                if (    $this->Object->getState() === MageOrder::STATE_COMPLETE 
                    ||  $this->Object->getState() === MageOrder::STATE_CLOSED 
                        ) {
                    $this->Out[$FieldName]  = True;
                } else {
                    $this->Out[$FieldName]  = False;
                }
                break;            
            case 'isPaid':
                if (    $this->Object->getState() === MageOrder::STATE_PROCESSING 
                    ||  $this->Object->getState() === MageOrder::STATE_COMPLETE 
                    ||  $this->Object->getState() === MageOrder::STATE_CLOSED 
                        ) {
                    $this->Out[$FieldName]  = True;
                } else {
                    $this->Out[$FieldName]  = False;
                }
                break;    
                
            //====================================================================//
            // TRACEABILITY INFORMATIONS
            //====================================================================//

            case 'updated_at':
                $this->Out[$FieldName] = date( SPL_T_DATETIMECAST, Mage::getModel("core/date")->timestamp($this->Object->getData($FieldName)));
                break;
                    
            default:
                return;
        }
        
        unset($this->In[$Key]);
    }    
    
}
