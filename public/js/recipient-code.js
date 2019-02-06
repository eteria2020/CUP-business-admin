/**
 *
 * @param recipientCode
 * @returns {*}
 */
function isRecipientCode(recipientCode)
{
    recipientCode = recipientCode.toUpperCase();

    return recipientCode.match(/^([A-Z0-9]){7}$/);
}