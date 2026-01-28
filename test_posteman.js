{
  "info": {
    "_postman_id": "tawheedconnect-api-final",
    "name": "TawheedConnect API - Complete",
    "description": "Collection complète pour tester l'API TawheedConnect avec authentification par téléphone",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "01 - Test & Utilitaires",
      "item": [
        {
          "name": "Ping API",
          "request": {
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "{{base_url}}/ping",
              "host": ["{{base_url}}"],
              "path": ["ping"]
            },
            "description": "Vérifie que l'API fonctionne correctement"
          },
          "response": []
        }
      ]
    },
    {
      "name": "02 - Authentification (Public)",
      "item": [
        {
          "name": "1. Register - Membre",
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 201) {",
                  "    var jsonData = pm.response.json();",
                  "    pm.environment.set('token', jsonData.token);",
                  "    pm.environment.set('verification_code', jsonData.verification_code);",
                  "    console.log('Token sauvegardé:', jsonData.token);",
                  "    console.log('Code de vérification:', jsonData.verification_code);",
                  "}"
                ],
                "type": "text/javascript"
              }
            }
          ],
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
              "raw": "{{base_url}}/auth/register",
              "host": ["{{base_url}}"],
              "path": ["auth", "register"]
            },
            "description": "Inscription d'un nouveau membre. Le token est automatiquement sauvegardé."
          },
          "response": []
        },
        {
          "name": "2. Register - Association",
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 201) {",
                  "    var jsonData = pm.response.json();",
                  "    pm.environment.set('token', jsonData.token);",
                  "    pm.environment.set('verification_code', jsonData.verification_code);",
                  "}"
                ],
                "type": "text/javascript"
              }
            }
          ],
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
              "raw": "{\n    \"first_name\": \"Moussa\",\n    \"last_name\": \"Sow\",\n    \"phone\": \"221779876543\",\n    \"password\": \"password123\",\n    \"password_confirmation\": \"password123\",\n    \"role\": \"association\",\n    \"city\": \"Thiès\",\n    \"email\": \"moussa@example.com\",\n    \"association_name\": \"Association Islamique de Thiès\",\n    \"association_description\": \"Promouvoir l'éducation islamique\"\n}",
              "options": {
                "raw": {
                  "language": "json"
                }
              }
            },
            "url": {
              "raw": "{{base_url}}/auth/register",
              "host": ["{{base_url}}"],
              "path": ["auth", "register"]
            },
            "description": "Inscription d'une association"
          },
          "response": []
        },
        {
          "name": "3. Verify Phone",
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
              "raw": "{\n    \"phone\": \"221771234567\",\n    \"code\": \"{{verification_code}}\"\n}",
              "options": {
                "raw": {
                  "language": "json"
                }
              }
            },
            "url": {
              "raw": "{{base_url}}/auth/verify-phone",
              "host": ["{{base_url}}"],
              "path": ["auth", "verify-phone"]
            },
            "description": "Vérifier le téléphone avec le code reçu. Utilisez le code des logs Laravel."
          },
          "response": []
        },
        {
          "name": "4. Resend Code",
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 200) {",
                  "    var jsonData = pm.response.json();",
                  "    pm.environment.set('verification_code', jsonData.verification_code);",
                  "    console.log('Nouveau code:', jsonData.verification_code);",
                  "}"
                ],
                "type": "text/javascript"
              }
            }
          ],
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
              "raw": "{{base_url}}/auth/resend-code",
              "host": ["{{base_url}}"],
              "path": ["auth", "resend-code"]
            },
            "description": "Renvoyer un nouveau code de vérification"
          },
          "response": []
        },
        {
          "name": "5. Login",
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 200) {",
                  "    var jsonData = pm.response.json();",
                  "    pm.environment.set('token', jsonData.token);",
                  "    console.log('Token sauvegardé:', jsonData.token);",
                  "}"
                ],
                "type": "text/javascript"
              }
            }
          ],
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
              "raw": "{{base_url}}/auth/login",
              "host": ["{{base_url}}"],
              "path": ["auth", "login"]
            },
            "description": "Connexion - Le token est automatiquement sauvegardé"
          },
          "response": []
        },
        {
          "name": "6. Forgot Password",
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 200) {",
                  "    var jsonData = pm.response.json();",
                  "    pm.environment.set('reset_code', jsonData.verification_code);",
                  "    console.log('Code de reset:', jsonData.verification_code);",
                  "}"
                ],
                "type": "text/javascript"
              }
            }
          ],
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
              "raw": "{{base_url}}/auth/forgot-password",
              "host": ["{{base_url}}"],
              "path": ["auth", "forgot-password"]
            },
            "description": "Demander un code de réinitialisation"
          },
          "response": []
        },
        {
          "name": "7. Reset Password",
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
              "raw": "{\n    \"phone\": \"221771234567\",\n    \"code\": \"{{reset_code}}\",\n    \"password\": \"newpassword123\",\n    \"password_confirmation\": \"newpassword123\"\n}",
              "options": {
                "raw": {
                  "language": "json"
                }
              }
            },
            "url": {
              "raw": "{{base_url}}/auth/reset-password",
              "host": ["{{base_url}}"],
              "path": ["auth", "reset-password"]
            },
            "description": "Réinitialiser le mot de passe avec le code"
          },
          "response": []
        }
      ]
    },
    {
      "name": "03 - Authentification (Protégé)",
      "item": [
        {
          "name": "Me (Profil utilisateur)",
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
              "raw": "{{base_url}}/auth/me",
              "host": ["{{base_url}}"],
              "path": ["auth", "me"]
            },
            "description": "Obtenir les informations de l'utilisateur connecté"
          },
          "response": []
        },
        {
          "name": "User (Alias de Me)",
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
              "raw": "{{base_url}}/auth/user",
              "host": ["{{base_url}}"],
              "path": ["auth", "user"]
            },
            "description": "Alias de /auth/me"
          },
          "response": []
        },
        {
          "name": "Logout",
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
              "raw": "{{base_url}}/auth/logout",
              "host": ["{{base_url}}"],
              "path": ["auth", "logout"]
            },
            "description": "Déconnexion - Supprime le token actuel"
          },
          "response": []
        }
      ]
    },
    {
      "name": "04 - Paiements",
      "item": [
        {
          "name": "Initiate Payment",
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
              },
              {
                "key": "Authorization",
                "value": "Bearer {{token}}",
                "type": "text"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"amount\": 1000,\n    \"phone\": \"221771234567\",\n    \"payment_method\": \"orange_money\"\n}",
              "options": {
                "raw": {
                  "language": "json"
                }
              }
            },
            "url": {
              "raw": "{{base_url}}/payments/initiate",
              "host": ["{{base_url}}"],
              "path": ["payments", "initiate"]
            },
            "description": "Initier un paiement (Orange Money, Wave, Free Money)"
          },
          "response": []
        }
      ]
    },
    {
      "name": "05 - Upload Fichiers",
      "item": [
        {
          "name": "Upload Photo de Profil",
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
            "body": {
              "mode": "formdata",
              "formdata": [
                {
                  "key": "photo",
                  "type": "file",
                  "src": [],
                  "description": "Sélectionnez une image (JPG, PNG, max 5MB)"
                }
              ]
            },
            "url": {
              "raw": "{{base_url}}/upload/photo",
              "host": ["{{base_url}}"],
              "path": ["upload", "photo"]
            },
            "description": "Uploader une photo de profil"
          },
          "response": []
        },
        {
          "name": "Upload Logo Association",
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
            "body": {
              "mode": "formdata",
              "formdata": [
                {
                  "key": "logo",
                  "type": "file",
                  "src": [],
                  "description": "Sélectionnez un logo (JPG, PNG, max 5MB)"
                }
              ]
            },
            "url": {
              "raw": "{{base_url}}/upload/logo",
              "host": ["{{base_url}}"],
              "path": ["upload", "logo"]
            },
            "description": "Uploader un logo pour une association"
          },
          "response": []
        }
      ]
    },
    {
      "name": "06 - Tests de Validation",
      "item": [
        {
          "name": "Register - Erreur (champs manquants)",
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
              "raw": "{\n    \"first_name\": \"Test\",\n    \"phone\": \"221771234567\"\n}",
              "options": {
                "raw": {
                  "language": "json"
                }
              }
            },
            "url": {
              "raw": "{{base_url}}/auth/register",
              "host": ["{{base_url}}"],
              "path": ["auth", "register"]
            },
            "description": "Test de validation - Devrait retourner erreur 422"
          },
          "response": []
        },
        {
          "name": "Login - Erreur (mauvais mot de passe)",
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
              "raw": "{\n    \"phone\": \"221771234567\",\n    \"password\": \"wrongpassword\"\n}",
              "options": {
                "raw": {
                  "language": "json"
                }
              }
            },
            "url": {
              "raw": "{{base_url}}/auth/login",
              "host": ["{{base_url}}"],
              "path": ["auth", "login"]
            },
            "description": "Test - Devrait retourner erreur 422"
          },
          "response": []
        }
      ]
    }
  ],
  "variable": [
    {
      "key": "base_url",
      "value": "http://127.0.0.1:8000/api",
      "type": "string"
    },
    {
      "key": "token",
      "value": "",
      "type": "string"
    },
    {
      "key": "verification_code",
      "value": "",
      "type": "string"
    },
    {
      "key": "reset_code",
      "value": "",
      "type": "string"
    }
  ]
}