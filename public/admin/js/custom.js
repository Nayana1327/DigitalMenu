function deleteCategory(id)
{
    if(confirm('Are you sure to delete this category?')){
        $.ajax({
            type: "POST",
            url: "category-delete",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

function deletePortion(id)
{
    if(confirm('Are you sure to delete this portion?')){
        $.ajax({
            type: "POST",
            url: "portion-delete",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

function deleteCuisine(id)
{
    if(confirm('Are you sure to delete this cuisine?')){
        $.ajax({
            type: "POST",
            url: "cuisine-delete",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

function deleteMenu(id)
{
    if(confirm('Are you sure to delete this item?')){
        $.ajax({
            type: "POST",
            url: "menu-delete",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

function deleteWaiter(id)
{
    if(confirm('Are you sure to delete this waiter?')){
        $.ajax({
            type: "POST",
            url: "waiter-delete",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

function deleteTable(id)
{
    if(confirm('Are you sure to delete this table?')){
        $.ajax({
            type: "POST",
            url: "table-delete",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

function unactivateMenu(id)
{
    if(confirm('Are you sure to UnActivate this Item?')){
        $.ajax({
            type: "POST",
            url: "menu-unactivate",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

function activateMenu(id)
{
    if(confirm('Are you sure to Activate this Item?')){
        $.ajax({
            type: "POST",
            url: "menu-activate",
            data: {
                id : id
            },
            headers: {
                    'X-CSRF-Token': $('meta[name = "csrf-token"]').attr('content')
            },
            success: function(response) {
              if(response.status == "success")
              {
                toastr.success(response.message);
              }
              else
              {
                toastr.error(response.message);
              }
              setTimeout(function () {
                location.reload(true);
                }, 2000);
            }
        }); 
    }
}

