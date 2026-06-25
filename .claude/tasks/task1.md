# Tasks

- Analisar, implementar as regras de negócio abaixo e as mensagens de erro neste projeto

- Nome do Brechó
  - O campo deve ser obrigatório
  - Minimo 5 caracteres
  - Máx 20 caracteres
  - Não pode conter apenas números
  - Não pode conter caracteres especiais no início/fim 

- Regras para E-mail
  - O campo deve ser obrigatório
  - Formato válido para e-mails
  - E-mail único no sistema
  - Máx 255 caracteres
​​
- Regras para Senha (Login/Cadastro)
  - O campo deve ser obrigatório
  - Minimo 6 caracteres
  - Máx 20 caracteres
  - Pelo menos 1 letra maiúscula
  - Pelo menos 1 letra minúscula
  - Pelo menos 1 número
  - Senha e confirmação iguais
  
- Regras para Produtos (CRUD)
  - Minimo 5 caracteres
  - O nome do produto deve ser obrigatório
  - Preço deve ser maior que zero
  - Preço máximo R$ 10.000,00
  - Quantidade em estoque entre 0 e 10
  - Descrição máxima 2000 caracteres
  - Imagem obrigatória no cadastro
  - Tamanho deve ser um dos valores pré-definidos

## Mensagens

### 4.7 Mensagens de Erro e Exceção

| Código | Mensagem                                                              |
| --------| -----------------------------------------------------------------------|
| MSG-01 | E-mail ou senha inválidos                                             |
| MSG-02 | Conta bloqueada. Tente novamente em 15 minutos                        |
| MSG-03 | Sessão expirada. Faça login novamente                                 |
| MSG-04 | O e-mail é obrigatório                                                |
| MSG-05 | A senha é obrigatória                                                 |
| MSG-06 | O nome do brechó é obrigatório                                        |
| MSG-07 | O nome deve ter no mínimo 5 caracteres                                |
| MSG-08 | O nome deve ter no máximo 20 caracteres                               |
| MSG-09 | Este e-mail já está cadastrado em outro brechó                        |
| MSG-10 | Digite um e-mail válido (ex: contato@brecho.com)                      |
| MSG-11 | A senha deve ter no mínimo 6 caracteres                               |
| MSG-12 | A senha deve conter pelo menos 1 letra maiúscula                      |
| MSG-13 | A senha deve conter pelo menos 1 letra minúscula                      |
| MSG-14 | A senha deve conter pelo menos 1 número                               |
| MSG-15 | As senhas não coincidem                                               |
| MSG-16 | Digite um telefone válido com DDD                                     |
| MSG-17 | Você deve aceitar os termos de uso para continuar                     |
| MSG-18 | Link expirado. Solicite uma nova recuperação de senha                 |
| MSG-19 | E-mail não encontrado                                                 |
| MSG-20 | Token inválido. Solicite uma nova recuperação                         |
| MSG-21 | Produto indisponível                                                  |
| MSG-22 | O nome do produto é obrigatório                                       |
| MSG-23 | O nome do produto deve ter no mínimo 5 caracteres                     |
| MSG-24 | O preço deve ser maior que R$ 0,00                                    |
| MSG-25 | O preço não pode ultrapassar R$ 10.000,00                             |
| MSG-26 | A quantidade em estoque deve ser entre 0 e 10                         |
| MSG-27 | A descrição deve ter no máximo 2000 caracteres                        |
| MSG-28 | É necessário enviar pelo menos uma imagem do produto                  |
| MSG-29 | Selecione uma categoria válida                                        |
| MSG-30 | Selecione um tamanho válido (PP, P, M, G, GG, XG, Único)              |
| MSG-31 | Selecione o estado de conservação                                     |
| MSG-32 | Formato não suportado. Use JPG ou PNG                                 |
| MSG-33 | Imagem excede o limite de 2MB                                         |
| MSG-34 | Produto não encontrado                                                |
| MSG-35 | Quantidade indisponível. Estoque atual: X unidades                    |
| MSG-36 | A quantidade deve ser pelo menos 1                                    |
| MSG-37 | Limite de 5 unidades por produto                                      |
| MSG-38 | Produto esgotado. Não é possível adicionar ao carrinho                |
| MSG-39 | Seu carrinho está vazio                                               |
| MSG-40 | Carrinho atingiu o limite de 20 produtos diferentes                   |
| MSG-41 | Produto esgotado. Removido do carrinho                                |
| MSG-42 | Preço alterado. Verifique os valores antes de finalizar               |
| MSG-43 | Sistema indisponível. Tente novamente mais tarde                      |
| MSG-44 | A operação demorou muito. Tente novamente                             |
| MSG-45 | Erro ao enviar imagem. Tente novamente                                |
| MSG-46 | Máximo de 5 imagens por produto                                       |
| MSG-47 | Você precisa estar logado para acessar esta página                    |
| MSG-48 | Sessão ativa em outro dispositivo. Deseja encerrar a sessão anterior? |
| MSG-49 | WhatsApp não configurado. Entre em contato pelo telefone cadastrado   |
| MSG-50 | O nome não pode conter apenas números                                 |
| MSG-51 | O nome não pode começar ou terminar com caracteres especiais          |
| MSG-52 | A senha deve ter no máximo 20 caracteres                              |
| MSG-53 | O domínio do e-mail parece inválido                                   |
| MSG-54 | O e-mail deve ter no máximo 255 caracteres                            |
| MSG-55 | O telefone deve ter 10 ou 11 dígitos (com DDD)                        |
| MSG-56 | O telefone para WhatsApp é obrigatório                                |
| MSG-57 | E-mail já cadastrado                                                  |