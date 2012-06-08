<?php

/**
 * Get all categories using the ebay Shopping API
 * Reference for the GetCategoryInfo API Call: http://developer.ebay.com/devzone/shopping/docs/callref/GetCategoryInfo.html
 * I would probably store all of the information in a database so you don't have to waste your API calls
 *
 * Output as of version 775
 * <?xml version="1.0" encoding="utf-8"?>
 * <GetCategoryInfoResponse xmlns="urn:ebay:apis:eBLBaseComponents">
 *   <!-- Standard Output Fields -->
 *   <Ack> AckCodeType </Ack>
 *   <Build> string </Build>
 *   <CorrelationID> string </CorrelationID>
 *   <Errors> ErrorType
 *     <ErrorClassification> ErrorClassificationCodeType </ErrorClassification>
 *     <ErrorCode> token </ErrorCode>
 *     <ErrorParameters ParamID="string"> ErrorParameterType
 *       <Value> string </Value>
 *     </ErrorParameters>
 *     <!-- ... more ErrorParameters nodes here ... -->
 *     <LongMessage> string </LongMessage>
 *     <SeverityCode> SeverityCodeType </SeverityCode>
 *     <ShortMessage> string </ShortMessage>
 *   </Errors>
 *   <!-- ... more Errors nodes here ... -->
 *   <Timestamp> dateTime </Timestamp>
 *   <Version> string </Version>
 *   <!-- Call-specific Output Fields -->
 *   <CategoryArray> CategoryArrayType
 *     <Category> CategoryType
 *       <CategoryID> string </CategoryID>
 *       <CategoryIDPath> string </CategoryIDPath>
 *       <CategoryLevel> int </CategoryLevel>
 *       <CategoryName> string </CategoryName>
 *       <CategoryNamePath> string </CategoryNamePath>
 *       <CategoryParentID> string </CategoryParentID>
 *       <LeafCategory> boolean </LeafCategory>
 *     </Category>
 *     <!-- ... more Category nodes here ... -->
 *   </CategoryArray>
 *   <CategoryCount> int </CategoryCount>
 *   <CategoryVersion> string </CategoryVersion>
 *   <UpdateTime> dateTime </UpdateTime>
 * </GetCategoryInfoResponse>
 */
class Ebay_Category {

    /**
     *
     */
    function get_categories($app_id, $site_id, $version, $category_id, $recursive=true, $encoding='JSON')
    {
        // make call to ebay API
        $url = "http://open.api.ebay.com/Shopping?callname=GetCategoryInfo";
        $url .= "&appid=".$app_id;
        $url .= "&siteid=".$site_id;
        $url .= "&version=".$version;
        if($recursive) {
            $url .= "&IncludeSelector=ChildCategories";
        }
        $url .= "&responseencoding=".$encoding;
        $url .= "CategoryID=".$category_id;

        $results = json_decode(file_get_contents($url));

        // check if the call was successful
        if($results->Ack != "Success") {
            // handle error
        } else {
            // loop through results
            foreach($results->CategoryArray->Category as $result) {

                // if this guy is not a leaf, then make function call
                if(isset($result->LeafCategory) && !$result->LeafCategory) {
                    $this->get_categories($result->CategoryID);
                }
    
                // slow things down a little bit
                sleep(2);
            }
        }
    }
}

// Example
//$ebay = new Ebay_Category();
//$ebay->get_categories('', '0', '729', '-1');
