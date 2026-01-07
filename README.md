# Paperless Rules

> An advanced rule engine for Paperless-NGX that brings intelligent automation to your document management workflow.

---

## What is this?

If you're using [Paperless-NGX](https://github.com/paperless-ngx/paperless-ngx) to manage your documents, you've probably run into situations where the built-in rules aren't quite flexible enough. That's where Paperless Rules comes in.

This Laravel-based application connects to your Paperless-NGX instance via API and lets you create sophisticated automation rules that go beyond what's possible out of the box. Think of it as a companion tool that watches your documents and applies complex logic to organize them exactly the way you need.

---

## Key Features

### Advanced Rule Engine
Create multi-condition rules that can handle complex scenarios. Chain conditions together, use AND/OR logic, and trigger multiple actions based on document properties.

### AI-Powered Document Analysis
Integration with [Ollama](https://ollama.ai) lets you use local language models to understand your documents. The AI can extract information like invoice numbers, dates, or amounts, and suggest appropriate tags and categories based on actual content, not just keyword matching.

Here's the important part: the AI doesn't make decisions on its own. It works within your rule framework. For example, when extracting dates, the AI identifies potential dates in the document, but your rules validate them before any changes are applied. You stay in control.

### Privacy-Focused
All AI processing happens locally on your infrastructure. Nothing leaves your system.

### Multi-Language Support
Built-in internationalization makes it easy to use in different languages.

### Real-Time Processing
Documents are processed as they arrive in Paperless-NGX, so your organization stays up to date automatically.

---
