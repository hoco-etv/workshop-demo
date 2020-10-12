if (document.readyState == 'loading') {
    document.addEventListener('DOMContentLoaded', ready)
} else {
    ready()
}

class cartObject {
    constructor(store, order_no, description, quantity) {
        this.store = store; // component store
        this.order_no = order_no; // product number of component
        this.description = description; // custom description
        this.quantity = quantity; // quantity to be ordered
        this.HTML = this.updateHTML();
    }

    updateHTML() {
            const stores = {
                0: "RS-logo.png",
                1: "farnell-logo.png"
            }
            var HTML = `<div id="${this.store}:${this.order_no}" class="cart-item">
                            <div class="item-image">
                                <img src="/img/stores/${stores[this.store]}">
                            </div>
                            <div class="item-info">
                                <p>Product Number: ${this.order_no}</p>
                                <p class="item-description">${this.description}</p>
                            </div>
                            <div class="item-actions">
                                <div class="item-quantity-container">
                                    <a data-toggle="tooltip" title="Decrease quantity" class="glyphicon glyphicon-minus-sign"></a>
                                    <span class="item-quantity">
                                    ${this.quantity}
                                    </span>
                                    <a data-toggle="tooltip" title="Increase quantity" class="glyphicon glyphicon-plus-sign"></a>
                                </div>
                            </div>
                            <div class="item-delete">
                                <a data-toggle="tooltip" title="Delete" class="glyphicon glyphicon-trash"></a>
                            </div>
                        </div>`

            this.HTML = HTML;
            return HTML;
        }
        //overrides the default toJSON function to omit HTML
    toJSON = function() {
        return {
            "store": this.store,
            "order_no": this.order_no,
            "description": this.description,
            "quantity": this.quantity
        }
    }
}

// checks the model validation for '#add-component-form'
function checkInput(formID) {
    var $form = $(formID),
        data = $form.data('yiiActiveForm');
    $.each(data.attributes, function() {
        this.status = 3;
    });
    $form.yiiActiveForm('validate');

    if ($form.find('.has-error').length) {
        // console.log('errors found');
        return false;
    } else {
        // console.log('no errors found');
        return true;
    }
}


function ready() {
    // localStorage.removeItem("shoppingCart", "") //reset
    // console.log("HARD RESET ENABLED")

    getCartFromLocalStorage();
    updateCart();
    updateEventListeners();

    var addToCartButtons = document.getElementsByClassName('add-item-button')
    for (var i = 0; i < addToCartButtons.length; i++) {
        var button = addToCartButtons[i];
        button.addEventListener('click', addToCartClicked);
    }

    document.getElementById('order_details-description').addEventListener('change', descriptionChanged) //sanitise description input
    document.getElementsByClassName('btn-purchase')[0].addEventListener('click', orderClicked)
    document.getElementsByClassName('btn-next')[0].firstElementChild.addEventListener('click', nextButtonClicked);
}

function updateEventListeners() {
    var removeCartItemButtons = document.getElementsByClassName('item-delete')
    for (var i = 0; i < removeCartItemButtons.length; i++) {
        var button = removeCartItemButtons[i]
        button.addEventListener('click', removeCartItem)
    }

    // Quantity increase listeners
    var quantityIncrease = document.getElementsByClassName('glyphicon-plus-sign')
    for (var i = 0; i < quantityIncrease.length; i++) {
        var input = quantityIncrease[i]
        input.addEventListener('click', quantityChanged)
    }

    // Quantity decrease listeners
    var quantityDecrease = document.getElementsByClassName('glyphicon-minus-sign')
    for (var i = 0; i < quantityDecrease.length; i++) {
        var input = quantityDecrease[i]
        input.addEventListener('click', quantityChanged)
    }
}

var cart = new Array(); //define array in which all products will be stored

function quantityChanged(event) {

    // check if plus or minus is clicked
    // add/decrease value
    // check if value does not go below 1
    // update cart to show new value

    var input = event.target
    var id = input.parentElement.parentElement.parentElement.id; //getting the id of the parent element (= product number)
    var store = id.split(':')[0];
    var order_no = id.split(':')[1];
    var delta = 0;

    if (input.className.includes('glyphicon-plus-sign')) {
        delta = 1;
    } else if (input.className.includes('glyphicon-minus-sign')) {
        delta = -1;
    }

    // find the corresponding component 
    // matching both order_no and store
    for (var index = 0, len = cart.length; index < len; index++) {
        if (cart[index].order_no === order_no && cart[index].store === store) {
            cart[index].quantity += delta;
            if (isNaN(cart[index].quantity) || cart[index].quantity <= 0) {
                cart[index].quantity = 1;
            } else if (cart[index].quantity >= 4294967295) {
                cart[index].quantity = 4294967295;
            }
            cart[index].updateHTML();
            updateCart();
            break;
        };
    }
    addCartToLocalStorage();
}

function descriptionChanged(event) {
    var input = event.target;
    input.value = input.value.split(",").join(" ") //sanitizing input
    input.value = input.value.split(";").join(" ")
}

function removeCartItem(event) {
    var id = event.target.parentElement.parentElement.id;
    var store = id.split(':')[0];
    var order_no = id.split(':')[1];
    for (var index = 0, len = cart.length; index < len; index++) {
        if (cart[index].order_no === order_no && cart[index].store === store) {
            cart.splice(index, 1);
            break;
        };
    }
    updateCart();
}

function addToCartClicked() {
    if (checkInput('#add-component-form')) {
        var store = document.getElementById('order_details-store').value;
        var order_no = document.getElementById('order_details-part_no').value;
        var description = document.getElementById('order_details-description').value;
        var quantity = parseInt(document.getElementById('order_details-quantity').value, 10);
        // console.log("Store: " + store + ", order_no: " + order_no + ", quantity: " + quantity + ", Description:" + description)

        addItemToCart(store, order_no, description, quantity);

        var notification = document.getElementsByClassName('component-added')[0].children[0];
        notification.style.display = 'unset';
        notification.className = "visible";
        setTimeout(function() { notification.className = "fadeOut"; }, 700);

    }
}


function addCartToLocalStorage() {
    localStorage.setItem('shoppingCart', JSON.stringify(cart));
    // console.log('Cart stored to local')
    // console.log(cart)
}

function getCartFromLocalStorage() {
    var temp_cart = JSON.parse(localStorage.getItem('shoppingCart'));
    if (temp_cart != null) {
        // console.log('Value of temp_cart: ')
        for (var index = 0, len = temp_cart.length; index < len; index++) {
            addItemToCart(temp_cart[index].store, temp_cart[index].order_no, temp_cart[index].description, temp_cart[index].quantity)
        }
    }
    // console.log('Cart loaded from local storage: ')
    // console.log(temp_cart)
}

function addItemToCart(store, order_no, description, quantity) {
    for (var index = 0, len = cart.length; index < len; index++) {
        if (cart[index].order_no === order_no && cart[index].store === store) {
            cart[index].quantity = parseInt(cart[index].quantity) + parseInt(quantity);
            cart[index].updateHTML();
            updateCart();
            var errorfield = document.getElementsByClassName("cart_input_error")[0];
            errorfield.innerHTML = "<p style='color:#a94442;'>This item is already present in your cart, the quantity has been updated";
            return;
        }
    }

    //if the item is not yet present in the shopping cart, execute this
    var errorfield = document.getElementsByClassName("cart_input_error")[0];
    errorfield.innerHTML = "<p style='color:#a94442;'></p>";
    cart.push(new cartObject(store, order_no, description, quantity))
    updateCart();
}

//replace the current cart as a whole by the items in the cart array
function updateCart() {
    var cartItems = document.getElementsByClassName('cart-items')[0] //get the element of cart-items
    var cartHTML = "";

    if (cart.length > 0) {
        for (var i = 0, len = cart.length; i < len; i++) {
            cartHTML += cart[i].HTML;
        }
    } else {
        cartHTML = 'Your shopping cart is empty';
    }

    cartItems.innerHTML = cartHTML;
    updateEventListeners();
    addCartToLocalStorage();
}


function orderClicked() {
    if (!checkInput('#customer-info-form')) {
        return;
    }

    var errorfield = document.getElementsByClassName('order-error-field')[0];
    if (cart.length == 0) {
        errorfield.innerHTML = "<p style='color:#a94442;margin-top:20px'>Your shopping cart cannot be empty, please add components!</p>";
        return;
    } else {
        errorfield.innerHTML = '';
    }

    var user_info = JSON.stringify({
        'email': document.getElementById('customer-email').value,
        'name': document.getElementById('customer-name').value,
        'student_no': document.getElementById('customer-student_no').value,
    });

    var jsonString = JSON.stringify(cart);

    $.ajax({
        url: '/order/submit',
        type: 'post',
        data: {
            cart: jsonString,
            customer: user_info,
        },
        success: function(jsonData) {
            var data = JSON.parse(jsonData);
            if (data['status']) {
                alert(data['message']);
                clearShoppingcart();
                location.reload();
            } else {
                alert(data['message'])
            }
        }
    });
}

function clearShoppingcart() {
    cart = new Array();
    updateCart();
}

function nextButtonClicked(event) {
    var deleteElements = document.getElementsByClassName('item-delete');
    var plusElements = document.getElementsByClassName('glyphicon-plus-sign');
    var minusElements = document.getElementsByClassName('glyphicon-minus-sign');


    for (var i = 0, len = plusElements.length; i < len; i++) {
        plusElements[i].style.display = 'none';
    }
    for (var i = 0, len = minusElements.length; i < len; i++) {
        minusElements[i].style.display = 'none';
    }
    for (var i = 0, len = deleteElements.length; i < len; i++) {
        deleteElements[i].style.display = 'none';
    }

    event.target.style.display = 'none';
}

function bulkAddClicked() {
    var lines = document.getElementsByClassName('bulkInputField')[0].firstElementChild.value.split('\n');
    var errorfield = document.getElementsByClassName('bulk_error_field')[0];

    lines.forEach(function(element, index) {
        var line = element.split(','); // output: [store, part number, quantity, description]
        if (line.length < 3) {
            console.log('line ' + (index + 1) + ' is empty')
        } else {

            var store = line[0];
            var part_no = line[1];
            var quantity = line[2];
            var description = line[3];

            var validated = validateBulk(store, part_no, quantity, description);
            if (!validated) {
                console.log('error on line ' + index);
                errorfield.innerHTML = "<p style='color:#a94442;'>Line " + (index + 1) + " contains an error.</p>";
                return;
            } else {
                errorfield.innerHTML = "<p style='color:#a94442;'></p>";
                Vstore = validated[0];
                Vpart_no = validated[1];
                Vquantity = validated[2];
                Vdescription = validated[3];
            }

            addItemToCart(Vstore, Vpart_no, Vdescription, Vquantity);

            // show checkmark
            var notification = document.getElementsByClassName('component-added')[1].children[0];
            notification.style.display = 'unset';
            notification.className = "visible";
            setTimeout(function() { notification.className = "fadeOut"; }, 700);
        }
    });
}

function validateBulk(store, part_no, quantity, description) {
    // validate store
    if (store.toLowerCase().indexOf('rs') != -1) {
        store = '0';
    } else if (store.toLowerCase().indexOf('farnell') != -1) {
        store = '1';
    } else {
        return false;
    }

    // validate part number
    part_no = part_no.replace(/\D/g, '');
    if (part_no > 4294967295) { part_no = 4294967295 }

    // validate quantity
    quantity = parseInt(quantity, 10);
    if (quantity > 4294967295) { quantity = 4294967295 }

    // validate description
    if (description == undefined) {
        description = '';
    }
    if (description.length > 100) {
        return false;
    }

    // validate combined values
    if (isNaN(store) || isNaN(part_no) || isNaN(quantity)) {
        return false;
    }

    return [store, part_no, quantity, description]
}