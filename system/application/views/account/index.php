<style>
    .tr-group{
        display:none;
    }
    .tr-ledger{
        display:none;
    }
    .td-group,.td-group label{
        cursor:pointer;
    }
</style>
<script type="text/javascript">
    var mode=true;
    var processTr = function(currElem,spaceCount){
        var display='table-row';
        if(mode==false)
            display='none';
        var nextnbspStr=currElem.nextSibling.children[0].innerHTML;
        var nextnbspCount=((nextnbspStr.match(/&nbsp;/g) || []).length);
        nextnbspCount=(nextnbspCount-1)/6;
        if(spaceCount==nextnbspCount)
            return;
        else{
            var innerSign;
            if(spaceCount==(nextnbspCount-1)){
                currElem.nextSibling.style.display = display;
                innerSign=currElem.nextSibling.children[0].children[0].innerHTML;
            }
            if(innerSign=='-'){
                    currElem.nextSibling.style.display = display;
                    processTr(currElem.nextSibling,nextnbspCount);
            }
            processTr(currElem.nextSibling,spaceCount);
        }
    }
    $(document).ready(function() { 
        $('.tr-group').click(function(){
            if((this.children[0].children[0].innerHTML)=='+'){
                mode=true;
                this.children[0].children[0].innerHTML='-';
            }
            else{
                mode=false;
                this.children[0].children[0].innerHTML='+';
            }
            var nbspStr=this.children[0].innerHTML;
            var nbspCount = ((nbspStr.match(/&nbsp;/g) || []).length);
            nbspCount--;
            if(nbspCount>=6)
                nbspCount=nbspCount/6;
            processTr(this,nbspCount);                                                             
        });
    });
</script>
<?php
	$this->load->library('accountlist');
	echo "<table>";
	echo "<tr valign=\"top\">";
	$asset = new Accountlist();
	echo "<td>";
	$asset->init(0);
	echo "<table border=0 cellpadding=5 class=\"simple-table account-table\">";
	echo "<thead><tr><th>Account Name</th><th>Type</th><th>O/P Balance</th><th>C/L Balance</th><th></th></tr></thead>";
	$asset->account_st_main(-1);
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

