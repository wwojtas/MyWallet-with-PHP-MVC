// remember date in date field
Date.prototype.toDateInputValue = (function () {
  var local = new Date(this);
  local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
  return local.toJSON().slice(0, 10);
});
document.getElementById('date_of_income').value = new Date().toDateInputValue();


// validate income form

$(document).ready(function () {

  $('#incomeForm').validate({

    rules: {
      amount: 'required',
      date_of_income: 'required',
      income_category: {
        min: 1
      }
    },

    messages: {
      amount: {
        required: 'Podaj wartość przychodu',
        number: "Podaj poprawną liczbę"
      },
      date_of_income: 'Podaj datę',
      income_category: 'Podaj kategorię'
    },
    errorPlacement: function (error, element) {
      var name = $(element).attr("name");
      error.appendTo($("#" + name + "_validate"));
    },
  });

  $("#income_category").select2({
    ajax: {
      url: "/AddIncome/chooseIncome",
      type: "post",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      cache: true
    }
  });


});