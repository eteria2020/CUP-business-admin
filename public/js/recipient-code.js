/**
 *
 * @param recipientCode
 * @returns {*}
 */
function isRecipientCode(recipientCode)
{
    if(recipientCode==="XXXXXXX") { // foreign customer
        return true;
    } else {
        return recipientCode.match(/^([0-9]){7}$/);
    }
}