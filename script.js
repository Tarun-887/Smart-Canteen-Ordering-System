
let cart = [];

function addToCart(name, price) {
    let item = cart.find(food => food.name === name);

    if (item) {
        item.quantity++;
    } else {
        cart.push({
            name: name,
            price: price,
            quantity: 1
        });
    }

    displayCart();
}

function changeQuantity(index, amount) {
    cart[index].quantity += amount;

    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }

    displayCart();
}

function displayCart() {
    let cartDiv = document.getElementById("cart");
    let total = 0;

    cartDiv.innerHTML = "";

    if (cart.length === 0) {
        cartDiv.innerHTML = "<p>Your cart is empty.</p>";
    }

    cart.forEach((item, index) => {
        let itemTotal = item.price * item.quantity;
        total += itemTotal;

        cartDiv.innerHTML += `
            <div class="cart-item">
                ${item.name} - \u20B9${item.price}
                <br>
                Quantity: ${item.quantity}
                <br>
                Subtotal: \u20B9${itemTotal}
                <br>
                <button type="button"
                onclick="changeQuantity(${index}, 1)">+</button>

                <button type="button"
                onclick="changeQuantity(${index}, -1)">-</button>
            </div>
        `;
    });

    document.getElementById("total").innerHTML =
        "Total: \u20B9" + total;
}

function prepareOrder() {
    if (cart.length === 0) {
        alert("Please add food items to your cart!");
        return false;
    }

    document.getElementById("cartData").value =
        JSON.stringify(cart);

    return true;
}
