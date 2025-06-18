# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 開発環境・コマンド

### Docker環境での開発
```bash
# 開発環境の起動
docker-compose up -d

# コンテナ内でのコマンド実行
docker exec -it my-closet-app bash

# アプリケーションURL
http://localhost:8080
```

### Laravel開発コマンド（src/ディレクトリ内で実行）
```bash
# 開発サーバー起動（並行実行）
composer run dev

# データベースマイグレーション
php artisan migrate

# シーダー実行
php artisan db:seed

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# テスト実行
composer run test
# または
php artisan test

# 単一テスト実行
php artisan test --filter=TestClassName

# コード整形
./vendor/bin/pint
```

### フロントエンド開発
```bash
# 依存関係インストール
npm install

# 開発用ビルド（ウォッチモード）
npm run dev

# 本番用ビルド
npm run build
```

## アーキテクチャ構造（DDD実装）

このアプリケーションはDomain-Driven Design（ドメイン駆動設計）を採用しており、以下の4層構造で設計されています：

### Domain層（ドメイン層）
- **場所**: `src/app/Domain/`
- **役割**: ビジネスロジックの中核を担う
- **構成要素**:
  - **Entities**: ビジネス概念を表現（Clothes, Category, Coordinate, User）
  - **ValueObjects**: 値オブジェクト（Size, Color, Brand）
  - **Repositories**: データアクセスのインターフェース定義

### Application層（アプリケーション層）
- **場所**: `src/app/Application/`
- **役割**: ユースケースの実現とドメインオブジェクトの調整
- **構成要素**:
  - **Services**: アプリケーションサービス（ClothesService, CategoryService等）
  - **DTOs**: データ転送オブジェクト

### Infrastructure層（インフラストラクチャ層）
- **場所**: `src/app/Infrastructure/`
- **役割**: 外部システムとの連携（データベース、ファイルシステム等）
- **構成要素**:
  - **Repositories**: Eloquentを使用したリポジトリ実装

### Presentation層（プレゼンテーション層）
- **場所**: `src/app/Http/`, `src/resources/`
- **役割**: ユーザーインターフェースとHTTPリクエスト処理
- **構成要素**:
  - **Controllers**: HTTPリクエスト処理
  - **Views**: Bladeテンプレート
  - **Middleware**: リクエスト前後処理

## 主要ドメインモデル

### 洋服管理（Clothes）
- **エンティティ**: `Domain/Entities/Clothes.php`
- **値オブジェクト**: Size, Color, Brand
- **主要機能**: 洋服の登録、更新、削除、検索（サイズ・色・ブランド別）

### カテゴリー管理（Category）
- **エンティティ**: `Domain/Entities/Category.php`
- **階層構造**: 親子関係を持つカテゴリー管理

### コーディネート管理（Coordinate）
- **エンティティ**: `Domain/Entities/Coordinate.php`
- **関係**: 複数の洋服を組み合わせたコーディネート

## 依存関係の方向

```
Presentation → Application → Domain ← Infrastructure
```

- Domain層は他の層に依存しない
- Application層はDomain層のみに依存
- Infrastructure層はDomain層に依存（リポジトリインターフェースを実装）
- Presentation層はApplication層とDomain層に依存

## 認証システム

Laravel Breezeを使用した認証機能が実装されています：
- ユーザー登録・ログイン
- メール認証
- パスワードリセット
- プロフィール管理

## データベース

- **本番**: MySQL 8.0
- **テスト**: SQLite（インメモリ）
- **マイグレーション**: `src/database/migrations/`
- **シーダー**: `src/database/seeders/`

## フロントエンド技術

- **CSS**: Tailwind CSS
- **JavaScript**: Alpine.js
- **ビルドツール**: Vite
- **テンプレート**: Laravel Blade

## テストについて

- **場所**: `src/tests/`
- **構成**: Feature（機能テスト）、Unit（単体テスト）
- **認証テスト**: Laravel Breezeの認証機能テストが含まれる