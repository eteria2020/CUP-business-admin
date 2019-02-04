/**
 *
 * Function for manage Italian Vat Number
 *
 *
 * Source code from  https://gist.github.com/mahizsas/4347946
 * @param pi
 * @returns {string}
 */

function IsVatNumber(pi)
{
    const validCharacters = "0123456789";

    // if( pi === '' )  {
    //     return '';
    // }

    if( pi.length !== 11 ) {
        //return "La lunghezza della partita IVA non è corretta: la partita IVA dovrebbe essere lunga esattamente 11 caratteri.";
        return translate("sVatNumberErrorLength");
    }

    for(let i = 0; i < 11; i++ ){
        if( validCharacters.indexOf( pi.charAt(i) ) === -1 ) {
            //return "La partita IVA contiene un carattere non valido `" + pi.charAt(i) + "'. I caratteri validi sono le cifre.";
            return translate("sVatNumberErrorChars");
        }
    }

    let s = 0;
    for(let i = 0; i <= 9; i += 2 ) {
        s += pi.charCodeAt(i) - '0'.charCodeAt(0);
    }

    for(let i = 1; i <= 9; i += 2 ){
        let c = 2*( pi.charCodeAt(i) - '0'.charCodeAt(0) );
        if( c > 9 ) {
            c = c - 9;
        }
        s += c;
    }

    if( ( 10 - s%10 )%10 !== pi.charCodeAt(10) - '0'.charCodeAt(0) ) {
        //return "La partita IVA non è valida: il codice di controllo non corrisponde.";
        return translate("sVatNumberErrorNoMatch");
    }

    return '';
}