/** 
 * Add jQuery Validation plugin method for a valid password
 *
 * Valid passwords contain at least one letter and one number.
 */
$.validator.addMethod('validPassword',
  function (value) {
    if (value != '') {
      if (value.match(/.*[a-z]+.*/i) == null) {
        return false;
      }
      if (value.match(/.*\d.*/) == null) {
        return false;
      }
    }
    return true;
  },
  'Musi zawierać co najmniej jedną literę i jedną cyfrę.'
);