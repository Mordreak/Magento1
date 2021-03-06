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

namespace Splash\Local\Objects\ThirdParty;

use Splash\Core\SplashCore      as Splash;

// Magento Namespaces
use Mage;
use Mage_Customer_Exception;

/**
 * @abstract    Magento 1 Customers Main Fields Access
 */
trait MainTrait
{
    
    
    /**
    *   @abstract     Build Customers Main Fields using FieldFactory
    */
    private function buildMainFields()
    {
        //====================================================================//
        // Gender Name
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("gender_name")
                ->Name("Social title")
                ->MicroData("http://schema.org/Person", "honorificPrefix")
                ->isReadOnly();

        //====================================================================//
        // Gender Type
        $desc = "Social title" . " ; 0 => Male // 1 => Female // 2 => Neutral";
        $this->fieldsFactory()->Create(SPL_T_INT)
                ->Identifier("gender")
                ->Name("Social title")
                ->MicroData("http://schema.org/Person", "gender")
                ->Description($desc)
                ->AddChoice(0, "Male")
                ->AddChoice(1, "Femele")
                ->isNotTested();
        
        //====================================================================//
        // Date Of Birth
        $this->fieldsFactory()->Create(SPL_T_DATE)
                ->Identifier("dob")
                ->Name("Date of birth")
                ->MicroData("http://schema.org/Person", "birthDate");

        //====================================================================//
        // Company
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("company")
                ->Name("Company")
                ->MicroData("http://schema.org/Organization", "legalName")
                ->isReadOnly();
        
        //====================================================================//
        // Prefix
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("prefix")
                ->Name("Prefix name")
                ->MicroData("http://schema.org/Person", "honorificPrefix");
        
        //====================================================================//
        // MiddleName
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("middlename")
                ->Name("Middlename")
                ->MicroData("http://schema.org/Person", "additionalName");
        
        //====================================================================//
        // Suffix
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("suffix")
                ->Name("Suffix name")
                ->MicroData("http://schema.org/Person", "honorificSuffix");
        
//        //====================================================================//
//        // Address List
//        $this->fieldsFactory()->Create(self::ObjectId_Encode( "Address" , SPL_T_ID))
//                ->Identifier("address")
//                ->InList("contacts")
//                ->Name($this->spl->l("Address"))
//                ->MicroData("http://schema.org/Organization","address")
//                ->isReadOnly();
    }
    
    
    
    /**
     *  @abstract     Read requested Field
     *
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     *
     *  @return         none
     */
    private function getMainFields($Key, $FieldName)
    {
        //====================================================================//
        // READ Fields
        switch ($FieldName) {
            //====================================================================//
            // Customer Company Overriden by User Id
            case 'company':
                $this->Out[$FieldName] = $this->getCompany();
                break;
            
            //====================================================================//
            // Gender Name
            case 'gender_name':
                $this->Out[$FieldName] = $this->getGenderName();
                break;
            
            //====================================================================//
            // Gender Type
            case 'gender':
                $this->Out[$FieldName] = ($this->Object->getData($FieldName) == 2) ? 1 : 0 ;
                break;
                
            //====================================================================//
            // Customer Date Of Birth
            case 'dob':
                $this->Out[$FieldName] = date(
                        SPL_T_DATECAST, 
                        Mage::getModel("core/date")->timestamp($this->Object->getData($FieldName))
                        );
                break;

            //====================================================================//
            // Customer Extended Names
            case 'prefix':
            case 'middlename':
            case 'suffix':
                $this->Out[$FieldName] = $this->Object->getData($FieldName);
                break;
            
//            //====================================================================//
//            // Customer Address List
//            case 'address@contacts':
//                if ( !$this->getAddressList() ) {
//                   return;
//                }
//                break;
            default:
                return;
        }
        unset($this->In[$Key]);
    }
    
    /**
     *  @abstract     Read Customer Company Name
     *  @return       string
     */
    private function getCompany()
    {
        if (!empty($this->Object->getData('company'))) {
            return $this->Object->getData('company');
        }
        return "Magento1("  . $this->Object->getEntityId() . ")";
    } 
    
    /**
     *  @abstract     Read Customer Gender Name
     *  @return       string
     */
    private function getGenderName()
    {
        if (empty($this->Object->getData("gender"))) {
            Splash::trans("Empty");
        }
        if ($this->Object->getData("gender") == 2) {
            return "Femele";
        } else {
            return "Male";
        }
    }    
    
    /**
     *  @abstract     Write Given Fields
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     *
     *  @return         none
     */
    private function setMainFields($FieldName, $Data)
    {
        //====================================================================//
        // WRITE Fields
        switch ($FieldName) {
            //====================================================================//
            // Gender Type
            case 'gender':
                //====================================================================//
                // Convert Gender Type Value to Magento Values
                // Splash Social title ; 0 => Male // 1 => Female // 2 => Neutral
                // Magento Social title ; 1 => Male // 2 => Female
                $Data++;
                //====================================================================//
                // Update Gender Type
                $this->setData($FieldName, $Data);
                break;
            //====================================================================//
            // Customer Date Of Birth
            case 'dob':
                $CurrentDob = date(
                        SPL_T_DATECAST, 
                        Mage::getModel("core/date")->timestamp($this->Object->getData($FieldName))
                        );
                if ($CurrentDob != $Data) {
                    $this->Object->setData($FieldName, Mage::getModel("core/date")->gmtDate(null, $Data));
                    $this->needUpdate();
                }
                break;
            
            //====================================================================//
            // Customer Extended Names
            case 'prefix':
            case 'middlename':
            case 'suffix':
                $this->setData($FieldName, $Data);
                break;
                
            default:
                return;
        }
        unset($this->In[$FieldName]);
    }
}
