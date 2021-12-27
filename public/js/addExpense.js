// remember date in date field
Date.prototype.toDateInputValue = (function () {
  var local = new Date(this);
  local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
  return local.toJSON().slice(0, 10);
});
document.getElementById('date_of_expense').value = new Date().toDateInputValue();

// <!-- validate and select2 scripts  Fetch instruction-->
$(document).ready(function () {

  $('#expenseForm').validate({
    rules: {
      amount: 'required',
      date_of_expense: 'required',
      expense_category: {
        min: 1
      },
      payment_method: {
        min: 1
      }
    },
    messages: {
      amount: {
        required: 'Podaj wartośc wydatku',
        number: "Podaj poprawną liczbę",
      },
      date_of_expense: 'Podaj datę',
      expense_category: 'Wybierz kategorię wydatku',
      payment_method: 'Wybierz sposób płatności'
    },

    errorPlacement: function (error, element) {
      var name = $(element).attr("name");
      error.appendTo($("#" + name + "_validate"));
    },
  });

  $("#expense_category").select2({
    ajax: {
      url: "/AddExpense/chooseExpenseCategory",
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

  $("#payment_method").select2({
    ajax: {
      url: "/AddExpense/choosePaymentMethod",
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

  // FETCH instruction

  const url = '/AddExpense/getSumExpenseCatagory';
  const inputTable = document.getElementById("limitTable");
  let limit = document.getElementById("limit");
  let issued = document.getElementById("issued");
  let difference = document.getElementById("difference");
  let finaly = document.getElementById("finally");
  inputTable.style.display = "none";
  let selectedCategory = "";
  let inputValue = 0;
  let date = new Date();
  let firstDayOfCurrentMonth = new Date(date.getFullYear(), date.getMonth(), 1);
  let firstDayOfCurrentMonthCompare = firstDayOfCurrentMonth.getTime();

  $("#date_of_expense").on("click", function () {
    selectedCategory = $("#expense_category option:last-child").text();
    inputValue = $("#amount").val();

    let dateInInput = new Date(document.getElementById('date_of_expense').value);
    let dateInInputCompare = dateInInput.getTime();

    if (dateInInputCompare < firstDayOfCurrentMonthCompare) {
      inputTable.style.display = "none";
    } else {
      fetch(url).then(res => res.json())
        .then(res => {
          res.forEach(el => {
            if (el['name'] == selectedCategory) {
              if (el['expense_limit'] == 0) {
                inputTable.style.display = "none";
              } else {
                limit.textContent = el['name'] + ":  " + el['expense_limit'];
                issued.textContent = el['sumCategory'];
                difference.textContent = el['expense_limit'] - el['sumCategory'];
                finaly.textContent = difference.textContent - inputValue;
                inputTable.style.display = "block";
              }
            }
          });
        })
    }
  });


  $("#amount").on("input", function () {
    selectedCategory = $("#expense_category option:last-child").text();
    inputValue = $("#amount").val();

    let dateInInput = new Date(document.getElementById('date_of_expense').value);
    let dateInInputCompare = dateInInput.getTime();

    if (dateInInputCompare < firstDayOfCurrentMonthCompare) {
      inputTable.style.display = "none";
    } else {
      fetch(url).then(res => res.json())
        .then(res => {
          res.forEach(el => {
            if (el['name'] == selectedCategory) {
              if (el['expense_limit'] == 0) {
                inputTable.style.display = "none";
              } else {
                limit.textContent = el['name'] + ":  " + el['expense_limit'];
                issued.textContent = el['sumCategory'];
                difference.textContent = el['expense_limit'] - el['sumCategory'];
                finaly.textContent = difference.textContent - inputValue;
                inputTable.style.display = "block";
              }
            }
          });
        })
    }
  });

  $("#expense_category").on("change", function () {
    selectedCategory = $("#expense_category option:last-child").text();
    inputValue = $("#amount").val();

    let dateInInput = new Date(document.getElementById('date_of_expense').value);
    let dateInInputCompare = dateInInput.getTime();

    if (dateInInputCompare < firstDayOfCurrentMonthCompare) {
      inputTable.style.display = "none";
    } else {
      fetch(url).then(res => res.json())
        .then(res => {
          res.forEach(el => {
            if (el['name'] == selectedCategory) {
              if (el['expense_limit'] == 0) {
                inputTable.style.display = "none";
              } else {
                limit.textContent = el['name'] + ":  " + el['expense_limit'];
                issued.textContent = el['sumCategory'];
                difference.textContent = el['expense_limit'] - el['sumCategory'];
                finaly.textContent = difference.textContent - inputValue;
                inputTable.style.display = "block";
              }
            }
          });
        })
    }
  });


});