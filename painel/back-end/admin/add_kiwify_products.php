<?php
session_start();
ob_start();
include_once('../../../config.php');

// JSON recebido
$json = '{
    "count": 4385,
    "marketplaceProducts": [
        {
            "id": "81642210-ca7d-11ee-8a1d-5bfbd99d06b3",
            "name": "Aulão: Contrato para PEGUE E MONTE (Como preencher e enviar contratos)",
            "product_img": "https://aws-assets.kiwify.com.br/5VWVjBHahJOd059/Contratos_e898db6b93544f049837ce72d9dbf287.jpeg",
            "price": 6900,
            "owner": "Bruno Zacher",
            "commission": 3000,
            "short_id": "9yLkigwt",
            "category": 3
        },
        {
            "id": "af727f60-3c54-11ee-b1dc-57bc811bccd6",
            "name": "KIT DO PREGADOR",
            "product_img": "https://aws-assets.kiwify.com.br/XEQXjj8EQ7lt3AG/product-300-x-250-px-1_f255b19625624de7bde74ea709c6d052.png",
            "price": 4700,
            "owner": "EITEOLOGO",
            "commission": 7000,
            "short_id": "mLlGDjIL",
            "category": 4
        },
        {
            "id": "0559dca0-ab51-11ee-a6a8-bf22c18d3edc",
            "name": "Red Pianos Gold Edition",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/qMRKfg7OQDQHJZ2/Capa-Grupo-Gold-2.4_ec5bc72a8c8c43369610c27a7bdf2a59.jpg",
            "price": 25000,
            "owner": "Eduardo Bortolato",
            "commission": 3000,
            "short_id": "bO9EAlzW",
            "category": 10
        },
        {
            "id": "d051acc0-a224-11ee-9186-690ddd5294b7",
            "name": "Bíblia do Pregador Pentecostal (Versão Digital)",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/gPVa1YHsE5J8Rws/Untitled_8bb05267f6d04dbc9ac859287f9104d2_2864fe99efec47ff96a3bac965d6fce4.png",
            "price": 2490,
            "owner": "Academia Bíblica Digital",
            "commission": 6500,
            "short_id": "Hf30nKr4",
            "category": 4
        },
        {
            "id": "0ec9b860-e6c9-11ee-b2e6-bfc742b52995",
            "name": "Curso 100% online de Cargos e Salários",
            "product_img": "https://aws-assets.kiwify.com.br/grZDLufgTUUvquv/capa-cargos-e_5ed5210da2f747cca0c7d068132ed7f7.png",
            "price": 84621,
            "owner": "Ricardo Campos",
            "commission": 3200,
            "short_id": "vlrQguoQ",
            "category": 3
        },
        {
            "id": "d3c07bc0-c083-11ee-9148-df1f182e079d",
            "name": "Iniciando na moda praia artesanal 2024",
            "product_img": "https://aws-assets.kiwify.com.br/VPDUyERmTvG5zH9/Top-marmelada-2_524e4a7f46144dc5ae0e16a2b8c725dd.png",
            "price": 49700,
            "owner": "Vanessa Mires da Rocha",
            "commission": 1800,
            "short_id": "3p2aEiRc",
            "category": 16
        },
        {
            "id": "23d95930-0038-11ef-9f88-f5e719ccf2bc",
            "name": "O princípio",
            "product_img": "https://aws-assets.kiwify.com.br/yOseNXM75KIxBkG/WhatsApp-Image-2024-04-26-at-15.57.49_27fde4a14ebd45ebb0f85e0cfbc143fb.jpeg",
            "price": 49700,
            "owner": "Eduarda Escobar",
            "commission": 3000,
            "short_id": "MEaknLP6",
            "category": 13
        },
        {
            "id": "a6006e90-2fe0-11ee-ac44-b7f15e0e605c",
            "name": "Curso de Laço Comprido - Alan Soares",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/BVUaS880gMvx3nl/ALAN-SOARES---CURSO-LACO_b2bc0a63f27c4eefa1b22ff3c2aa52e9.png",
            "price": 49700,
            "owner": "Bernardo Schiochet",
            "commission": 2000,
            "short_id": "OIeDhj5N",
            "category": 16
        },
        {
            "id": "0e99d2b0-eeb4-11ee-afc4-0d79338b8d9d",
            "name": "Template Equilíbrio Produtivo",
            "product_img": "https://aws-assets.kiwify.com.br/dsMe9JLJfps6Qet/Equilibrio-Produtivo-v2-7_48ff5ac5f02d4ce6a7c3a20d6f6a85ad.png",
            "price": 5790,
            "owner": "Guilherme Pessoa",
            "commission": 5000,
            "short_id": "KCF8NknR",
            "category": 13
        },
        {
            "id": "b5dd3600-ea75-11ed-a826-a380929840d2",
            "name": "Viver de Automação Residencial",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/QAWPiT23OsiPDHc/Criativos-Newtek-Post-para-Facebook-Paisagem_743a29340b614bc8a2a7a4c4e6de8ca2.jpg",
            "price": 11700,
            "owner": "Vladimir Freire",
            "commission": 5000,
            "short_id": "HgtHfleK",
            "category": 3
        },
        {
            "id": "d603b0b0-c1b9-11ed-a020-cb35c22228e6",
            "name": "Aprenda como Fazer Falta - Super Combo",
            "product_img": "https://aws-assets.kiwify.com.br/HZkZIAhEOH4raPW/livro-aprendacomofazerfalta_09dbd6e1a6954b86b3a8a6871b096b09.png",
            "price": 6700,
            "owner": "Editora GPR",
            "commission": 5000,
            "short_id": "CJuMnRqs",
            "category": 2
        },
        {
            "id": "358135f0-9306-11ec-aaf0-4f7237d9b9b3",
            "name": "WaTalk",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/GQa4DgfVoWZf5E2/2_1b80ef3210f34b60b1544876bb6023f2.png",
            "price": 8700,
            "owner": "Christian Ramon",
            "commission": 5000,
            "short_id": "iemjUbq0",
            "category": 10
        },
        {
            "id": "4901a460-7ffa-11ee-bc56-878e1b90aeb6",
            "name": "Credenciamento Sistema HealthyStart PRO 2024",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/kufIjFXckMEw7Za/PPT-Credenciamento-HealthyStart-PRO-2023-Anderson---1_fbb367d7885648d584538a3b29c26663.jpg",
            "price": 49000,
            "owner": "Orthoghia",
            "commission": 1000,
            "short_id": "tSPdXWxn",
            "category": 16
        },
        {
            "id": "73397590-5eae-11ee-ad63-496dbd3259dc",
            "name": "Curso Seja Contratado",
            "product_img": "https://aws-assets.kiwify.com.br/yxDDGVqc9UJKC0x/cel-capa-kiwify-3_16c9ae5058af4f88a091314dc763bebd.png",
            "price": 39700,
            "owner": "Lorrana Silva",
            "commission": 1000,
            "short_id": "E6ZaANZX",
            "category": 3
        },
        {
            "id": "74de4380-b923-11ec-9740-f14388b16f4e",
            "name": "Escola do Marketing Digital - Método NH",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/Fd6YhWcolpjtF4R/Kiwify_bf1369c1352a4cd483f63c8302580375.jpg",
            "price": 39700,
            "owner": "NH Distribuição de Conteúdo LTDA",
            "commission": 6700,
            "short_id": "kFs0QgVf",
            "category": 22
        },
        {
            "id": "e9d343c0-9b27-11ed-a28f-cb890c5f91a0",
            "name": "Kit de Materiais ENEM 2024",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/TbcBcoxzcnftuQF/clique-no-link-da-bio-300-x-250-px_2b0e91f309ed46ee84ccf848d714fef0.png",
            "price": 19900,
            "owner": "VESTMapaMental",
            "commission": 4000,
            "short_id": "4bDWosbd",
            "category": 16
        },
        {
            "id": "bed73080-d3d2-11ed-b0d5-c339d18369df",
            "name": "Creator Academy",
            "product_img": "https://aws-assets.kiwify.com.br/1p4zSfJ9Ga58Lsf/asdsssa_b49a73ec7ce942209f164d266b9ff7d0.png",
            "price": 42700,
            "owner": "Natália Volkmer",
            "commission": 2500,
            "short_id": "tJqiGOPw",
            "category": 22
        },
        {
            "id": "cc8d19c0-8740-11ee-a17c-c5a6bd8f0645",
            "name": "MÉTODO MASTER LASH",
            "product_img": "https://aws-assets.kiwify.com.br/RiHl6TJnYmnv4Aa/Copia-de-Capa-do-produto-Dourado---1_a72d86f207b540da8282e59d36f8b50a.png",
            "price": 29700,
            "owner": "Diuliane Lima",
            "commission": 3000,
            "short_id": "U3cqcNrs",
            "category": 14
        },
        {
            "id": "50ce3850-1cd4-11ed-a3b9-73b16d43131b",
            "name": "Curso \"A ética e a política da não-violência: Judith Butler conversa com Foucault, Fanon e Benjamin\" com Ernani Chaves",
            "product_img": "https://aws-assets.kiwify.com.br/DEfgGuDyLmpusxc/1_05f1f221300a46fca3599367f5b1f37d.png",
            "price": 9700,
            "owner": "Filosofia Cultura Política",
            "commission": 8500,
            "short_id": "7pPBdy6P",
            "category": 16
        },
        {
            "id": "107e2f90-f445-11ee-8bcb-d93618dc704c",
            "name": "Comunidade Prospect VIP",
            "product_img": "https://aws-assets.kiwify.com.br/lNblwtBmiiZ4j2T/logotipo_horizontal_fundo_transparente-1_cf0c1be6745f442898a1094ca57bf0c9.png",
            "price": 79700,
            "owner": "Vítor Ito",
            "commission": 5000,
            "short_id": "LRmTUfwl",
            "category": 0
        },
        {
            "id": "87ed4a10-3965-11ef-ac28-d9c411ea4f93",
            "name": "Em um relacionamento abusivo com a ansiedade",
            "product_img": "https://aws-assets.kiwify.com.br/ImZS2lUCqPuto5l/capa-hotmart_83e78611914943bd8706c7fd80179ce3.jpg",
            "price": 3990,
            "owner": "Lucas Terapeuta",
            "commission": 5000,
            "short_id": "KdSr0xn5",
            "category": 13
        },
        {
            "id": "5e0ead80-1f48-11ed-9e37-195e197fe44a",
            "name": "Escola de Piscineiros",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/qcmWPfxYFzPJ2kg/dsa_e4e58f0ec030460e934634eda4b5fc16.jpeg",
            "price": 39790,
            "owner": "Paulo R. Esteves",
            "commission": 3000,
            "short_id": "FlZvYolW",
            "category": 16
        },
        {
            "id": "c6cd09f0-88ad-11ee-b649-cbf2e172cde5",
            "name": "Expert Ads - Especialista em Vendas Facebook Ads",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/OqFi3Ku9wt6m1Ll/capa-Expert_9c4218d4460a4da49ffae177a3015b9e.jpg",
            "price": 39790,
            "owner": "Filipe Detrey",
            "commission": 3000,
            "short_id": "OVsrNNo7",
            "category": 22
        },
        {
            "id": "d3049ae0-80be-11ee-88c7-35576a123465",
            "name": "VIDAS RESTAURADAS, CASAMENTO RECONCILIADO",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/efSLT0jHEebJraI/1699737665_ca56f13d0da048809052149d4cd4c5a9.png",
            "price": 2990,
            "owner": "Vidas Restauradas, Casamento Reconciliado",
            "commission": 5500,
            "short_id": "219whaT5",
            "category": 2
        },
        {
            "id": "57e22d10-120d-11ef-b2d9-c1da7846a4bc",
            "name": "Método REVOLUZ",
            "product_img": "https://aws-assets.kiwify.com.br/F4y1BCBWEYT9ixG/CAPA-PRODUTO_4ccbf04cb32c4ec88be9cd2f3db19ed7.png",
            "price": 39700,
            "owner": "Jordana Cantarelli",
            "commission": 5000,
            "short_id": "ktAVyN4p",
            "category": 4
        },


        {
            "id": "af17c270-2f4a-11ef-9461-07e7cd79b53f",
            "name": "REPROGRAME",
            "product_img": "https://aws-assets.kiwify.com.br/VZfs1vjq3jbDAUB/Captura-de-Tela-2024-06-10-as-12.49.19_61c31af79bb84251902339e93179b56f.png",
            "price": 39700,
            "owner": "Auge Capusso",
            "commission": 5000,
            "short_id": "fqyDmMLJ",
            "category": 16
        },
        {
            "id": "144f1860-b344-11ee-9811-35bf54b63adc",
            "name": "[EC] Edição Criativa",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/q1l9GT4aYslfPf8/logo-edicao-criativa_0b8a352c561043ccbf9c97a96e5417fd.png",
            "price": 39700,
            "owner": "@manodoscriativos",
            "commission": 5000,
            "short_id": "gQihSlft",
            "category": 16
        },
        {
            "id": "57e22d10-120d-11ef-b2d9-c1da7846a4bc",
            "name": "Método REVOLUZ",
            "product_img": "https://aws-assets.kiwify.com.br/F4y1BCBWEYT9ixG/CAPA-PRODUTO_4ccbf04cb32c4ec88be9cd2f3db19ed7.png",
            "price": 39700,
            "owner": "Jordana Cantarelli",
            "commission": 5000,
            "short_id": "ktAVyN4p",
            "category": 4
        },
        {
            "id": "e72ace70-513a-11ef-8e4a-f91835dda69e",
            "name": "Psicologia Negra",
            "product_img": "https://aws-assets.kiwify.com.br/2qFv4N2VaNvIk85/IMG_7669_405f49ea9c054ea686e8a668daebd895.jpeg",
            "price": 7790,
            "owner": "Pedro Terra",
            "commission": 7000,
            "short_id": "PCCGh2bV",
            "category": 16
        },
        {
            "id": "d020d470-fb97-11ee-bdec-833cab6e9d4a",
            "name": "Clube das Líderes - Natura e Avon",
            "product_img": "https://aws-assets.kiwify.com.br/PJEtKvdvF7g3pDn/banner-pagina-celular-1840-x-1280-px-Post-para-Instagram-1_8775e06152cb4d0e85567005ad2d75b2.png",
            "price": 22490,
            "owner": "Helena Almeida",
            "commission": 2000,
            "short_id": "OkLi3U9Q",
            "category": 22
        },
        {
            "id": "ef3189e0-0c8c-11ee-8346-59497c3e388f",
            "name": "Consutoria On-line",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/IhCgaqvSMkqK1QV/A8C970C7-7A21-486C-ABE6-A262CA838F50_c64673f0326742359461dc0249d205a9.jpeg",
            "price": 44700,
            "owner": "Murilo Ferreira de Lima Junior",
            "commission": 2000,
            "short_id": "LmBgmz7U",
            "category": 0
        },
        {
            "id": "23a64e30-9466-11ed-9f5f-7dc6b7c985ab",
            "name": "DETETIVE PARTICULAR",
            "product_img": "https://aws-assets.kiwify.com.br/ztPbGXQoO2XFmug/CRIATIVO-00-17_b644495d77834c2a9c9db777a92f8312.png",
            "price": 19700,
            "owner": "Prof. Me. Uzian Pinto",
            "commission": 8500,
            "short_id": "d8lBqPJP",
            "category": 13
        },
        {
            "id": "044f3920-50a9-11ee-88f4-53f2d647a7aa",
            "name": "Curso de Montador Steel Frame",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/OdGeZP7YdRcUT8L/capa-do-curso3_ab20f0e7b324417abc2b52b4404f0c91.jpg",
            "price": 4700,
            "owner": "School-It Curso Steelframe",
            "commission": 5000,
            "short_id": "ZVJV96cF",
            "category": 3
        },
        {
            "id": "6e1b5250-b92b-11ed-b2d9-4d0ec0539e54",
            "name": "Comunidade Sabedoria Medicinal - Plantas Medicinais e Fitoterapia",
            "product_img": "https://aws-assets.kiwify.com.br/mkRNjv8rE6gYBEH/METODO-MP_dbdaec3592d64da38d08d4a15dcdc453.png",
            "price": 19700,
            "owner": "Breno Duarte",
            "commission": 6000,
            "short_id": "HBTAcdkU",
            "category": 0
        },
        {
            "id": "d7355660-cc8f-11ed-919a-dff3f09f4178",
            "name": "Fórmula do PLANO B",
            "product_img": "https://aws-assets.kiwify.com.br/kD60dOE1ReBum6m/banner-mobile_fee2a6b6cbe040889f222f0efb91beea.jpg",
            "price": 69700,
            "owner": "Mariana Ambrósio",
            "commission": 4000,
            "short_id": "8fpOV9vk",
            "category": 3
        },
        {
            "id": "329ee500-0bf2-11ef-88c9-9573c2587be5",
            "name": "Hacks mentais",
            "product_img": "https://aws-assets.kiwify.com.br/gq1vacDiKER0Y42/Captura-de-tela-2024-05-14-202351_34b2c281db4c49fdb800655430642c76.png",
            "price": 72000,
            "owner": "Luan Gama",
            "commission": 3500,
            "short_id": "9hWMUG64",
            "category": 16
        },
        {
            "id": "ede6ee60-ef75-11ed-aa87-8f381dde4f98",
            "name": "Manual da Mulher de Sabedoria",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/shSpYFbT8m1dzZH/1683483152_b489c543ad34482c8a13e86a1ef81a53.png",
            "price": 2999,
            "owner": "Mulher de sabedoria",
            "commission": 7500,
            "short_id": "Z9y0lRWK",
            "category": 4
        },
        {
            "id": "93d899e0-f759-11ee-815a-890eb505b18c",
            "name": "POLÍCIA PENAL + POLÍCIA CIVIL",
            "product_img": "https://aws-assets.kiwify.com.br/6F75UICrfbWlXSq/DIVULGACAO-NOVA-300-x-250-px-8_03726f50764f46ffbd9451a67be6c080.png",
            "price": 21700,
            "owner": "Professor Rodrigo Janiques",
            "commission": 5000,
            "short_id": "L2H1GFTR",
            "category": 16
        },
        {
            "id": "c109f8e0-5b8c-11ef-8f4b-adcbfe0fae97",
            "name": "Roteiros para Anotações de Enfermagem",
            "product_img": "https://aws-assets.kiwify.com.br/8i1ScKXdXE9RT0l/ebook_3_D-removebg-preview_a9036da139934274a27e74dc9bc28004.png",
            "price": 2490,
            "owner": "Cristiane Correa",
            "commission": 5000,
            "short_id": "nXNrrz3G",
            "category": 0
        },
        {
            "id": "d6aedbe0-4cda-11ef-9101-775cddb9329e",
            "name": "Jarvis automatic 2.0",
            "product_img": "https://aws-assets.kiwify.com.br/JRq2gacWV7RaRBk/IMG_3821_c07b3c95899946b4b23cb4a8e3bd453f.jpeg",
            "price": 79900,
            "owner": "Higor policarpo",
            "commission": 5000,
            "short_id": "gbR0y8Di",
            "category": 1
        },
        {
            "id": "61764780-334b-11ef-b4c6-2b42cf85e5c9",
            "name": "Viralizei Aqui",
            "product_img": "https://aws-assets.kiwify.com.br/ClTCmST7XDFZRg1/1000121330_06454709d30a4e688dbd381a3f6d5f68.png",
            "price": 7790,
            "owner": "andrea januario",
            "commission": 6000,
            "short_id": "L75DR27n",
            "category": 22
        },
        {
            "id": "61b3e9e0-7ef9-11ec-b7fc-65f00a475474",
            "name": "Guia do Conteúdo Diário",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/Ov19KKp8eDhcGQq/guiimagem-do-produto-1000x1000_7d2dd20703e6422f9d61b0f68034b6a7.png",
            "price": 57600,
            "owner": "Michele DAgostini",
            "commission": 6000,
            "short_id": "8Amzr6lr",
            "category": 22
        },
        {
            "id": "d6550480-5cc6-11ee-b21a-316efe201f58",
            "name": "Método Vendas Virais",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/EU81S8tEz7zvexM/MVV_e881c9888dde4e93aa1712eeac0f0fe5.png",
            "price": 9700,
            "owner": "GLAUBER IURY UCHOA DE ABREU",
            "commission": 5000,
            "short_id": "zBskPwbG",
            "category": 22
        },
        {
            "id": "a45f1790-aa7e-11ed-97fd-9546f68bf3bd",
            "name": "Curso Completo Guitarra Gospel",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/L87TnLQs10ibI2l/Design-sem-nome-9_1ee532e8e32344f2b11fe1311bf5ca3d.png",
            "price": 3790,
            "owner": "Guitarra Gospel",
            "commission": 5000,
            "short_id": "pouE4M0b",
            "category": 20
        },
        {
            "id": "f86329c0-8481-11ee-8f18-4526bd404b5c",
            "name": "Mentoria JS",
            "product_img": "https://aws-assets.kiwify.com.br/TcAjhE5SrwxdCJ5/SUPORTE-400-x-400-px-300-x-300-px-500-x-500-px-400-x-400-px_8c74b12ce79a49ec945447651602b21e.png",
            "price": 129700,
            "owner": "Jessica Sandi",
            "commission": 4000,
            "short_id": "V4XUeBkD",
            "category": 22
        },
        {
            "id": "cc3b0dd0-3c70-11ee-8b40-6ff2308b4077",
            "name": "Estrutura Orgânico Milionário By @eu.wesleysilv4",
            "product_img": "https://aws-assets.kiwify.com.br/TayZN9wWC5a4HBn/PERFIL-ORIGINAL_95c880c959834e85b574078bf9b860ef.jpg",
            "price": 8790,
            "owner": "@eu.wesleysilv4",
            "commission": 6000,
            "short_id": "z5UXjv4S",
            "category": 22
        },
        {
            "id": "7a253d80-ea10-11ec-9462-abd1dcc481e5",
            "name": "PerfectPages | Crie Páginas Incríveis",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/MskNf5gs8QBmCnw/topo-emails-area-de-membros_140120df551b4a9bad929aad08914052.jpg",
            "price": 9700,
            "owner": "Ross Digitall",
            "commission": 6000,
            "short_id": "rDa8ptHN",
            "category": 22
        },
        {
            "id": "e03f5a60-ef04-11ee-9675-f378d7140e38",
            "name": "MTV+ MULHERES TRANSFORMADAS VENDEM MAIS",
            "product_img": "https://aws-assets.kiwify.com.br/oQA4GQjE4qrOSNW/mulheres-transformadas-1_8887001fe8534708a810ca0dc55f0354.jpg",
            "price": 14700,
            "owner": "Thais Dias",
            "commission": 5000,
            "short_id": "w4BQmBQQ",
            "category": 22
        },
        {
            "id": "fa59b8d0-d80a-11ed-9ce9-6bdb7f495f67",
            "name": "Sketchup para makers e marceneiros",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/yEgraiIjL3F8biv/CapaCursoArquivoFinal350x250-2_2f72c0b726f145759bb100b94d116200.png",
            "price": 24900,
            "owner": "Diogo Menezes",
            "commission": 1500,
            "short_id": "Jgi6327A",
            "category": 12
        },
        {
            "id": "9dda8b70-0d8d-11ef-bdff-d3767f5ba2e0",
            "name": "E-book: Meditação Cristã Confissões de Fé®",
            "product_img": "https://aws-assets.kiwify.com.br/F4y1BCBWEYT9ixG/CAPA-PRODUTO_9204546964564f73a3a579eac2bf8c52.png",
            "price": 6990,
            "owner": "Jordana Cantarelli",
            "commission": 3000,
            "short_id": "G8uI0OKa",
            "category": 4
        },


        {
            "id": "05806300-fac7-11ee-8f0f-bf0c211302de",
            "name": "Curso de Saúde e Segurança do Trabalho - Do Pó à Potência",
            "product_img": "https://aws-assets.kiwify.com.br/Y8QmAkwNvwhUoXQ/Base-para-Mentoria---kiwify-300-x-250-px_c6267f67f76c43da9c28e1d771e473a3.png",
            "price": 75000,
            "owner": "Fernanda Lima",
            "commission": 500,
            "short_id": "d3z9ZF5D",
            "category": 16
        },
        {
            "id": "e9a50620-5b12-11ef-80a1-bfa510753af1",
            "name": "PACK DO DROP",
            "product_img": "https://aws-assets.kiwify.com.br/Xc8KYOFlTerq6ag/AVATAR-PERFIL_ae5a2a5ec0634fcb8b869ea3086bf21a.png",
            "price": 4990,
            "owner": "Rafael Lima",
            "commission": 4000,
            "short_id": "ec5XT3Qn",
            "category": 22
        },
        {
            "id": "4b5f5b00-2f34-11ef-8486-dded269c8632",
            "name": "Do Zero aos Mil reais como afiliado Shopee",
            "product_img": "https://aws-assets.kiwify.com.br/jkpoGrWau2dP1ET/WhatsApp-Image-2024-06-23-at-18.19.28_ca21bcb508f7407fa837948de1c92e8b.jpeg",
            "price": 4990,
            "owner": "Aline Paulino Rodrigues",
            "commission": 5000,
            "short_id": "RiMXrmxj",
            "category": 22
        },
        {
            "id": "9ecdf500-ee9c-11ed-a4ca-7b1eb9cba2c0",
            "name": "Mais de 300GB de artes digitais para sublimação 2024 | Arquivos para download",
            "product_img": "https://aws-assets.kiwify.com.br/1mGmw5D6wxsHUdU/capa-kiwify-002_0ea183f1c6a1474aae6c5c0f332c6e41.jpg",
            "price": 9799,
            "owner": "Inknarte Sublimação",
            "commission": 7000,
            "short_id": "Uf6fA8Rd",
            "category": 23
        },
        {
            "id": "be02b8e0-5906-11ef-afd7-11c621532299",
            "name": "Formação em Psiconefrologia - Turma 2",
            "product_img": "https://aws-assets.kiwify.com.br/kjUmgWKGOnNQc0q/JL12-1_c8231d36ea554f98bd24ce1a1f459578.png",
            "price": 15000,
            "owner": "Catavento",
            "commission": 1000,
            "short_id": "FjgVaI8m",
            "category": 16
        },
        {
            "id": "ad5b0da0-cf6b-11ee-8b23-07cf26a12756",
            "name": "TOP CLEANER",
            "product_img": "https://aws-assets.kiwify.com.br/xHOeXTlhSGwWy1Y/Captura-de-Tela-2024-02-19-as-18.16.30_6ffcc4a78be549888281c60d244b3b23.png",
            "price": 14990,
            "owner": "Thainan Brito",
            "commission": 1500,
            "short_id": "qrIuCt2z",
            "category": 3
        },
        {
            "id": "0ffc7b00-280f-11ef-b392-8ff89f729e81",
            "name": "Plug ML",
            "product_img": "https://aws-assets.kiwify.com.br/HCCNPrhgUKcV5mQ/Imagem-do-Produto-2C_5e8732507ab543fca7684582b3cee0f8.jpg",
            "price": 14901,
            "owner": "MARCELO CAMARGO - DROPSHIPPING PLUG LAR",
            "commission": 1000,
            "short_id": "yiKoMMmk",
            "category": 10
        },
        {
            "id": "1893c740-32ed-11ee-9361-cb6c828e10a4",
            "name": "Blindagem Individual",
            "product_img": "https://aws-assets.kiwify.com.br/tdJUiCQ6qUeQvaD/Post-Para-Instagram-Seguranca-Eletronica-Cameras-Moderno-Azul-E-Branco-2_048f3845d4a1421187a12d5efe116a79.png",
            "price": 54990,
            "owner": "@italofbotelho",
            "commission": 3000,
            "short_id": "iApLcwmc",
            "category": 18
        },
        {
            "id": "da5c4eb0-62d6-11ee-92c8-4900c217d4e4",
            "name": "PACK DE RESUMOS",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/NwXyX2JUX3QmatI/Design-sem-nome_be9cbde86c734c4aa15688882eecc899.png",
            "price": 3677,
            "owner": "Evelyn França",
            "commission": 5000,
            "short_id": "5inaIsN5",
            "category": 16
        },
        {
            "id": "f1a6ab80-44fd-11ed-a9b6-137e43f4f252",
            "name": "Mentoria Elite 6D",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/NGtR0BKeyQV26nh/2024-1_50e9bac6bc93462abac4bb38c39113b9.png",
            "price": 194700,
            "owner": "Silvio Roberto",
            "commission": 4000,
            "short_id": "2Xjpdxgm",
            "category": 22
        },
        {
            "id": "4ffb6d20-e22c-11ee-9d8b-1fc6b8f216c6",
            "name": "FL Studio Descomplicado | Micha",
            "product_img": "https://aws-assets.kiwify.com.br/ekg9HmrEdgHCksH/fl-studio-descomplicado-2_58ea31d13b4540f9b98376b295f44096.png",
            "price": 5499,
            "owner": "Micha-Alec Warkentin",
            "commission": 5000,
            "short_id": "PynC2wKF",
            "category": 20
        },
        {
            "id": "c4023330-1ee4-11ef-bf49-f7945cf6c26f",
            "name": "MÉTODO INFINTY DÓLAR 2.0",
            "product_img": "https://aws-assets.kiwify.com.br/JaomEqGk2dCyMLH/foto-produto_20d7dc6b95cd438aa1bdeecc91008650.png",
            "price": 39700,
            "owner": "ANA PAULA NOLETO",
            "commission": 6700,
            "short_id": "CIsdD9NN",
            "category": 22
        },
        {
            "id": "17e5efb0-771f-11ed-bab9-d9025b58b978",
            "name": "TRICOFLIX",
            "product_img": "https://aws-assets.kiwify.com.br/LWRrm9yR7UQBIgK/THUMB-KIWIFY_10aaea1aebec4c6faa239de15c5b2661.jpg",
            "price": 69700,
            "owner": "Angélica Gomes",
            "commission": 3000,
            "short_id": "IK76iG4u",
            "category": 17
        },
        {
            "id": "bdc12ee0-b148-11ee-8fdb-f7d8368c3ff0",
            "name": "Dance +40",
            "product_img": "https://aws-assets.kiwify.com.br/u7LMCPyMW2i4JVR/CAPA-MOBILE_23b92d315d24428a9d9b1febf30ba739.png",
            "price": 36000,
            "owner": "Artur Cândido",
            "commission": 5000,
            "short_id": "ZivaITTT",
            "category": 20
        },
        {
            "id": "501db3d0-309c-11ef-b111-0371a6c65f83",
            "name": "Apostila Hebraico Bíblico",
            "product_img": "https://aws-assets.kiwify.com.br/NV2qXf81b7LWLcf/21-98531-4883-Documento-A4-_20240606_101222_0000_0616930171a648e18a5140357645ec10.png",
            "price": 2700,
            "owner": "Cícero Pereira",
            "commission": 3000,
            "short_id": "Y4UnpiAt",
            "category": 8
        },
        {
            "id": "6277b570-0330-11ee-ae26-e1e753bbaff6",
            "name": "Formação Guardiãs do Sagrado Feminino",
            "product_img": "https://storage.googleapis.com/assets.kiwify.com.br/BosypdXG5IPjdAa/Tara-139_58f62121571b4485a4085ccd2a1d364d.jpg",
            "price": 44000,
            "owner": "Juliana Di Avalon",
            "commission": 2000,
            "short_id": "xNtxH25s",
            "category": 4
        },
        {
            "id": "b72fbd50-b7ef-11ee-8f0e-215c14731cb5",
            "name": "PACK EDIT PRO",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/KPldHrfkWPOtVls/Design-sem-nome-21_95eb6c07a9724fbeb222eb1175c04125.png",
            "price": 2700,
            "owner": "LUCAS EDITOR",
            "commission": 5000,
            "short_id": "2NQ5Blky",
            "category": 23
        },
        {
            "id": "be29a230-2fcf-11ef-9ba6-3787ec1e5995",
            "name": "PROTAGONISTA - Criando a sua persona de sucesso",
            "product_img": "https://aws-assets.kiwify.com.br/yOseNXM75KIxBkG/Post-Instagram-Lancamento-de-Curso-Online.png_8e0d4663ef504cd6a853628dd32e16bd.png",
            "price": 19900,
            "owner": "Eduarda Escobar",
            "commission": 5000,
            "short_id": "LJQAoUxS",
            "category": 13
        },
        {
            "id": "82832c90-0808-11ef-a62e-5d9203d19770",
            "name": "Combo TEA e TDAH",
            "product_img": "https://aws-assets.kiwify.com.br/SjtSBOoQTU5T0Dr/Carb-Cycling-a-Dieta-das-Celebridades-1_8d0ea5faa87647849fc050ef70425f23.png",
            "price": 1999,
            "owner": "Dr. João Gabriel",
            "commission": 8000,
            "short_id": "9bOJiqwQ",
            "category": 0
        },
        {
            "id": "1db7e960-5dc4-11ef-9ef8-3bf0c3d99784",
            "name": "Ebook Click Pro Nail",
            "product_img": "https://aws-assets.kiwify.com.br/bjnadZQHWnSgGTR/IMG_0679_778559ae734a421693ec5d5dda89cece.jpeg",
            "price": 9700,
            "owner": "Leticia Gama",
            "commission": 3000,
            "short_id": "G8UJrDwc",
            "category": 16
        },
        {
            "id": "88deca70-cb92-11ee-b5f9-7def35e4a7ff",
            "name": "Tudo sobre Provadores",
            "product_img": "https://aws-assets.kiwify.com.br/mFtNsKxJaewNHpf/capa-provador_13ffc6e4af0448f7a0be284ba7a9c5f3.png",
            "price": 19700,
            "owner": "Bia Tessari",
            "commission": 5000,
            "short_id": "pybD9qa9",
            "category": 14
        },
        {
            "id": "a6f13f10-9a0b-11ee-b8f4-215006199f3c",
            "name": "Método Caixa de Pandora",
            "product_img": "https://s3.amazonaws.com/aws-assets.kiwify.com.br/P2goEtoPm4RnGuK/WhatsApp-Image-2023-12-13-at-19.05.00_cd52565c04a04c619ea8573976015e99.jpeg",
            "price": 44700,
            "owner": "Thayná Tostes",
            "commission": 5000,
            "short_id": "93GOiqlP",
            "category": 22
        },
        {
            "id": "c45bbaa0-30af-11ee-9d4a-c31bd60fb555",
            "name": "Aulão do Brincar completo1",
            "product_img": "https://aws-assets.kiwify.com.br/8JYyBmV7KNju364/WhatsApp-Image-2024-09-03-at-21.07.04_e21e55fd43724504a832ac466af4da17.jpeg",
            "price": 44700,
            "owner": "Adriana Silva",
            "commission": 4000,
            "short_id": "NDT77oo4",
            "category": 16
        },
        {
            "id": "53e52ac0-e552-11ed-a032-4f29cff1e966",
            "name": "PACK FEED ORGANIZADO - ORGANIZAGRAM PREMIUM",
            "product_img": "https://aws-assets.kiwify.com.br/yE8tXrx9xeNLWzu/pack-creative-10_70bb61d9ad304bc88b7b1ef6c66fa594.png",
            "price": 6700,
            "owner": "Carol Rhein",
            "commission": 6000,
            "short_id": "41UJFei5",
            "category": 22
        },
        {
            "id": "f85c7560-4323-11ef-9c43-25bcbc6eb147",
            "name": "Foco em Harmonia",
            "product_img": "https://aws-assets.kiwify.com.br/vp7hjysZPYtkUIR/Logo-Minimalista-Cinza-Loja-4_0d9862e7978e4399b283764d356f211c.png",
            "price": 9790,
            "owner": "Marcos Cruz",
            "commission": 3000,
            "short_id": "1qhGVlfr",
            "category": 20
        },


    ]
}
';

// Ultima pagina 42

// Decodificar o JSON
$data = json_decode($json, true);

// Verificar se o JSON foi decodificado corretamente
if ($data === null) {
    die("Erro ao decodificar o JSON.");
}

// Função para verificar se o produto já existe
function produtoExiste($conn_pdo, $id) {
    $sql = "SELECT id FROM tb_kiwify_products WHERE short_id = :short_id";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':short_id', $id);
    $stmt->execute();
    
    return $stmt->rowCount() > 0;
}

// Inserir ou atualizar produtos
foreach ($data['marketplaceProducts'] as $product) {
    if (produtoExiste($conn_pdo, $product['short_id'])) {
        echo "Produto " . $product['name'] . " já está cadastrado.<br>";
    } else {
        // Converter `price` e `commission` (de centavos para reais)
        $price = $product['price'] / 100;
        $commission = $product['commission'] / 100;

        // Preparar a declaração SQL para inserção
        $sql = "INSERT INTO tb_kiwify_products (product_id, name, product_img, price, owner, commission, short_id, category) 
                VALUES (:product_id, :name, :product_img, :price, :owner, :commission, :short_id, :category)";
        
        $stmt = $conn_pdo->prepare($sql);

        // Bind dos parâmetros
        $stmt->bindParam(':product_id', $product['id']);
        $stmt->bindParam(':name', $product['name']);
        $stmt->bindParam(':product_img', $product['product_img']);
        $stmt->bindParam(':price', $price);  // Valor convertido
        $stmt->bindParam(':owner', $product['owner']);
        $stmt->bindParam(':commission', $commission);  // Valor convertido
        $stmt->bindParam(':short_id', $product['short_id']);
        $stmt->bindParam(':category', $product['category']);

        // Executar a inserção
        if (!$stmt->execute()) {
            echo "Erro ao inserir o produto: " . $product['name'] . "<br>";
        }
    }
}
