{
  "info": {
    "_postman_id": "tawheedconnect-api-collection",
    "name": "TawheedConnect API",
    "description": "Collection complète pour tester l'API TawheedConnect (authentification par téléphone)",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Test - Ping API",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "url": {
          "raw": "http://127.0.0.1:8000/api/ping",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "ping"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Register (Inscription)",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"first_name\": \"Aminata\",\n    \"last_name\": \"Diallo\",\n    \"phone\": \"221771234567\",\n    \"password\": \"password123\",\n    \"password_confirmation\": \"password123\",\n    \"role\": \"member\",\n    \"city\": \"Dakar\",\n    \"email\": \"aminata@example.com\"\n}",
          "options": {
            "raw": {
              "language": "json"
            }
          }
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/register",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "register"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Login (Connexion)",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"phone\": \"221771234567\",\n    \"password\": \"password123\"\n}",
          "options": {
            "raw": {
              "language": "json"
            }
          }
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/login",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "login"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Me (Utilisateur connecté)",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Authorization",
            "value": "Bearer {{token}}",
            "type": "text"
          }
        ],
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/me",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "me"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Verify Phone",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"phone\": \"221771234567\",\n    \"code\": \"1234\"\n}",
          "options": {
            "raw": {
              "language": "json"
            }
          }
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/verify-phone",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "verify-phone"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Resend Code",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"phone\": \"221771234567\"\n}",
          "options": {
            "raw": {
              "language": "json"
            }
          }
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/resend-code",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "resend-code"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Forgot Password",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"phone\": \"221771234567\"\n}",
          "options": {
            "raw": {
              "language": "json"
            }
          }
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/forgot-password",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "forgot-password"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Reset Password",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n    \"phone\": \"221771234567\",\n    \"code\": \"5678\",\n    \"password\": \"nouveau123\",\n    \"password_confirmation\": \"nouveau123\"\n}",
          "options": {
            "raw": {
              "language": "json"
            }
          }
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/reset-password",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "reset-password"]
        }
      },
      "response": []
    },
    {
      "name": "Auth - Logout",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          },
          {
            "key": "Authorization",
            "value": "Bearer {{token}}",
            "type": "text"
          }
        ],
        "url": {
          "raw": "http://127.0.0.1:8000/api/auth/logout",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "auth", "logout"]
        }
      },
      "response": []
    }
  ],
  "variable": [
    {
      "key": "token",
      "value": "",
      "description": "Colle ici le token reçu après login ou register"
    }
  ]
}