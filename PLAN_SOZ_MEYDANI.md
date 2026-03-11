# Söz Meydanı (Soru/Cevap) Modülü - Uygulama Planı

## Uygulama Sırası

- [x] 1. Migration'lar + Enum
- [x] 2. Modeller (QnaCategory, QnaQuestion, QnaAnswer, QnaLike)
- [x] 3. Service sınıfları (QnaCategoryService, QnaQuestionService, QnaAnswerService, QnaLikeService)
- [x] 4. FormRequest'ler (StoreQnaQuestionRequest, StoreQnaAnswerRequest, AdminQnaCategoryRequest)
- [x] 5. Mail sınıfları + mail template view'ları (4 mail)
- [x] 6. Controller'lar (Front QnaController + Admin 3 controller)
- [x] 7. Route tanımları (web.php)
- [x] 8. Front Blade view'lar (index, category, show)
- [x] 9. Admin Blade view'lar (kategoriler, sorular, cevaplar)
- [x] 10. JS dosyaları (front qna.js + admin qna.js)
- [x] 11. Permission seeder
- [x] 12. AppServiceProvider View Composer güncellemesi
- [x] 13. Admin sidebar'a Söz Meydanı menü ekleme

---

## 1. Migration'lar

### qna_categories
- id, name, slug (unique), description (nullable), icon, color_class, sort_order (default 0), is_active (default true)
- timestamps + softDeletes

### qna_questions
- id, user_id (FK), qna_category_id (FK), title, slug (unique), body (text)
- status (enum: pending/approved/rejected, default pending)
- view_count (default 0), like_count (default 0), answer_count (default 0)
- ip_address (nullable), timestamps + softDeletes
- Index: status, qna_category_id, user_id, created_at

### qna_answers
- id, qna_question_id (FK), user_id (FK), body (text)
- status (enum: pending/approved/rejected, default pending)
- like_count (default 0), ip_address (nullable)
- timestamps + softDeletes
- Index: status, qna_question_id, user_id

### qna_likes
- id, user_id (FK), likeable_id, likeable_type
- timestamps
- Unique: user_id + likeable_id + likeable_type

## 2. Enum
- QnaStatus: Pending, Approved, Rejected — label(), color(), icon()

## 3. Modeller
- QnaCategory: SoftDeletes, fillable, relationships, scopes
- QnaQuestion: SoftDeletes, fillable, relationships, scopes (approved, pending, byCategory)
- QnaAnswer: SoftDeletes, fillable, relationships, scopes
- QnaLike: fillable, relationships (morphTo)

## 4. Service'ler
- QnaCategoryService: CRUD + cache + stats
- QnaQuestionService: CRUD + filtre + approve/reject + mail dispatch + cache
- QnaAnswerService: CRUD + approve/reject + mail dispatch + cache
- QnaLikeService: toggle + hasLiked

## 5. FormRequest'ler
- StoreQnaQuestionRequest: title, body, qna_category_id
- StoreQnaAnswerRequest: body
- AdminQnaCategoryRequest: name, slug, description, icon, color_class, sort_order, is_active

## 6. Mail Sınıfları
- QnaQuestionSubmittedMail → admin'e
- QnaQuestionApprovedMail → soru sahibine
- QnaAnswerSubmittedMail → admin'e
- QnaAnswerApprovedMail → cevap sahibine

## 7. Controller'lar
### Front: QnaController
- index, category, show, storeQuestion, storeAnswer, toggleLike

### Admin: QnaCategoryController
- index, create, store, edit, update, destroy

### Admin: QnaQuestionController
- index, show, approve, reject, destroy

### Admin: QnaAnswerController
- index, approve, reject, destroy

## 8. Route'lar
### Front
- GET /soz-meydani
- GET /soz-meydani/{categorySlug}
- GET /soz-meydani/{categorySlug}/{questionSlug}
- POST /soz-meydani/soru-sor [auth, throttle]
- POST /soz-meydani/cevap-yaz/{question} [auth, throttle]
- POST /soz-meydani/begen [auth, throttle]

### Admin
- Resource routes for kategoriler, sorular, cevaplar

## 9. Blade View'lar
### Front
- front/qna/index.blade.php
- front/qna/category.blade.php
- front/qna/show.blade.php

### Admin
- admin/qna-categories/index.blade.php
- admin/qna-categories/form.blade.php
- admin/qna-questions/index.blade.php
- admin/qna-questions/show.blade.php
- admin/qna-answers/index.blade.php

### Mail
- emails/qna/ dizininde 4 template

## 10. JS Dosyaları
- public/js/qna.js (front)
- public/assets/admin/js/qna.js (admin)

## 11. Permission Seeder
- qna.view, qna.approve, qna.delete
- qna-categories.view, qna-categories.create, qna-categories.edit, qna-categories.delete

## 12. View Composer
- Admin sidebar'a pendingQnaCount ekleme

## 13. Admin Sidebar Menü
- Söz Meydanı parent menü + alt menüler (Kategoriler, Sorular, Cevaplar)
