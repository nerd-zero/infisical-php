# Infisical PHP Client

by [nerd-zero](https://packages.n0.rocks) – [n0.rocks](https://n0.rocks)

A PHP client library for interacting with the [Infisical](https://infisical.com/) secrets management platform. This package allows developers to programmatically fetch and manage secrets stored in Infisical within their PHP applications.

> ⚠️ **Warning**: This project is still a work in progress. APIs, features, and usage may change at any time without notice. Please use with caution and pin versions accordingly if used in production.

---

## 📦 Installation

Install via Composer:

```bash
composer require nerd-zero/infisical-php
```

---

## 📚 Table of Contents

- [Introduction](#introduction)
- [How to Use](#how-to-use)
- [Customization](#customization)
- [Supported Endpoints](#supported-endpoints)
  - [Authentication Management](#authentication-management)
  - [Folder Management](#folder-management)
  - [Secret Management](#secret-management)

---

## 🚀 Introduction

This is a PHP client to connect to the [Infisical](https://infisical.com/) Secrets Management API with minimal setup and ease of use.

### 🔧 Features

1. Easy to use
2. No need to manually generate or fetch tokens — handled internally
3. Only requires the Infisical base URI
4. Automatic JSON encoding/decoding
5. Works with clean associative arrays and native PHP data

---

## 🧪 How to Use

```php
use Infisical\InfisicalClient;

$client = InfisicalClient::factory(
    baseUri: 'your-infisical-base-uri',
    clientId: 'your-infisical-client-id',
    clientSecret: 'your-infisical-client-secret',
);

// Get all secrets
$secrets = $client->listSecrets([
    'workspaceId' => 'your-workspace-id',
    'environment' => 'your-environment',
    'path' => 'your-path',
]);
```

---

## ⚙️ Customization

> (Coming soon — details on middleware, request retries, logging, and more.)

---

## 📖 Supported Endpoints

### 🔐 [Authentication Management](https://infisical.com/docs/api-reference/endpoints/authentication)

| Description     | Function Name     | Supported |
|----------------|-------------------|-----------|
| —              | —                 | —         |

> _(Functions will be documented soon.)_

---

### 📁 [Folder Management](https://infisical.com/docs/api-reference/endpoints/folders)

| Description     | Function Name     | Supported |
|----------------|-------------------|-----------|
| List folders   | `listFolders()`    | ✔️        |
| Get by ID      | `getFolderById()`  | ✔️        |
| Create folder  | `createFolder()`   | ✔️        |
| Update folder  | `updateFolder()`   | ✔️        |
| Delete folder  | `deleteFolder()`   | ✔️        |

---

### 🔑 [Secret Management](https://infisical.com/docs/api-reference/endpoints/secrets)

| Description     | Function Name     | Supported |
|----------------|-------------------|-----------|
| List secrets   | `listSecrets()`    | ✔️        |
| Create secret  | `createSecret()`   | ✔️        |
| Get by name    | `retrieveSecret()` | ✔️        |
| Update secret  | `updateSecret()`   | ✔️        |
| Delete secret  | `deleteSecret()`   | ✔️        |
| Bulk Create Secrets     | `bulkCreateSecrets()`      | ✔️        |
| Bulk Update Secrets     | `bulkUpdateSecrets()`      | ✔️        |
| Bulk Delete Secrets     | `bulkDeleteSecrets()`      | ✔️        |
| Attach tags   | `attachTags()`      | ✔️        |
| Detach tags   | `detachTags()`      | ✔️        |

---
