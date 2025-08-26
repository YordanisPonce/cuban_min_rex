document.addEventListener("DOMContentLoaded", function () {
  let e = document.querySelector("#formAuthentication");
  e &&
    "undefined" != typeof FormValidation &&
    FormValidation.formValidation(e, {
      fields: {
        name: {
          validators: {
            notEmpty: {
              message: "Por favor ingrese su nombre",
            },
            stringLength: {
              min: 6,
              message: "El nombre debe tener más de 6 caracteres",
            },
          },
        },
        email: {
          validators: {
            notEmpty: {
              message: "Por favor ingrese su correo electrónico",
            },
            emailAddress: {
              message: "Por favor, introduzca una dirección de correo electrónico válida",
            },
          },
        },
        "email-name": {
          validators: {
            notEmpty: {
              message: "Por favor ingrese su correo electrónico o nombre",
            },
            stringLength: {
              min: 6,
              message: "El nombre debe tener más de 6 caracteres",
            },
          },
        },
        password: {
          validators: {
            notEmpty: {
              message: "Por favor ingrese su contraseña",
            },
            stringLength: {
              min: 6,
              message: "La contraseña debe tener más de 6 caracteres",
            },
          },
        },
        "confirm-password": {
          validators: {
            notEmpty: {
              message: "Por favor confirme su contraseña",
            },
            identical: {
              compare: () => e.querySelector('[name="password"]').value,
              message: "La contraseña y su confirmación no coinciden",
            },
            stringLength: {
              min: 6,
              message: "La contraseña debe tener más de 6 caracteres",
            },
          },
        },
        terms: {
          validators: {
            notEmpty: {
              message: "Por favor, acepte los términos y condiciones",
            },
          },
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: "",
          rowSelector: ".form-control-validation",
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
        autoFocus: new FormValidation.plugins.AutoFocus(),
      },
      init: (e) => {
        e.on("plugins.message.placed", (e) => {
          e.element.parentElement.classList.contains("input-group") &&
            e.element.parentElement.insertAdjacentElement("afterend", e.messageElement);
        });
      },
    });
  var a = document.querySelectorAll(".numeral-mask");
  0 < a.length &&
    a.forEach((a) => {
      a.addEventListener("input", (e) => {
        a.value = e.target.value.replace(/\D/g, "");
      });
    });
});
