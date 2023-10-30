<script>
    const passwordElement = document.getElementById("password");
    const confirmationElement = document.getElementById("password_confirmation");
    function updateList() {
        let criteria = { character: false, uppercase: false, lowercase: false, number: false, symbol: false };
        const password = passwordElement.value;

        criteria.character = password.length >= 8;
        criteria.uppercase = /[A-Z]/.test(password);
        criteria.lowercase = /[a-z]/.test(password);
        criteria.number = /[0-9]/.test(password);
        criteria.symbol = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\-]/.test(password);

        for (const requirement in criteria) {
            console.log(requirement, criteria[requirement]);
            const listItem = document.getElementById(requirement);
            const span = listItem.querySelector('span');
            if (criteria[requirement]) {
                span.textContent = '☑';
            } else {
                span.textContent = '×';
            }
        }
    }

    updateList();
    passwordElement.addEventListener("input", updateList);

    function updatePasswordConfirmation() {
        const password = passwordElement.value;
        const confirmation = confirmationElement.value;

        confirmationElement.setCustomValidity(password === confirmation ? "" : "The passwords must match!");
    }

    updatePasswordConfirmation();
    passwordElement.addEventListener("input", updatePasswordConfirmation);
    confirmationElement.addEventListener("input", updatePasswordConfirmation);
</script>
