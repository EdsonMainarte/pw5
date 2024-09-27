<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Estilização global */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            display: flex;
        }
        
        /* Estilização da sidebar */
        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #ddd;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar__container {
            padding: 20px;
        }
        
        .sidebar__user {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .sidebar__img img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        
        .sidebar__link {
            display: flex;
            align-items: center;
            padding: 10px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        
        .sidebar__link:hover {
            background-color: #f5f5f5;
        }
        
        .sidebar__link.active-link {
            background-color: #007bff;
            color: #fff;
        }
        
        /* Estilização do container principal */
        .container {
            margin-left: 270px; /* Deixe espaço para a sidebar */
            width: calc(100% - 270px);
            padding: 20px;
        }
        
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .header button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .header button:hover {
            background-color: #0056b3;
        }
        
        .header input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
        }
        
        .header .icons i {
            margin: 0 10px;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .header .icons i:hover {
            color: #007bff;
        }
        
        /* Estilização da tabela */
        .table-container {
            background-color: #fff;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .table-header {
            background-color: #f5f5f5;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background-color: #f5f5f5;
        }
        
        .table .status {
            display: flex;
            align-items: center;
        }
        
        .table .status span {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
        }
        
        .status-em-andamento { background-color: #ff9800; }
 .status-feito { background-color: #4caf50; }
        .status-parado { background-color: #f44336; }
        
        .priority-baixa { background-color: #2196f3; color: #fff; padding: 5px 10px; border-radius: 5px; }
        .priority-alta { background-color: #673ab7; color: #fff; padding: 5px 10px; border-radius: 5px; }
        .priority-media { background-color: #ffeb3b; color: #000; padding: 5px 10px; border-radius: 5px; }
        
        .add-task, .add-group {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #f5f5f5;
            border-top: 1px solid #ddd;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .add-task:hover, .add-group:hover {
            background-color: #e0e0e0;
        }
        
        .total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #f5f5f5;
            border-top: 1px solid #ddd;
        }
        
        .total span {
            font-weight: bold;
        }
        
        /* Estilização responsiva */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header input {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .table-container {
                padding: 10px;
            }
            
            .table th, .table td {
                font-size: 14px;
            }
            
            .total {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
                overflow-y: visible;
            }
            
            .sidebar__container {
                padding: 10px;
            }
            
            .header {
                padding: 10px;
            }
            
            .table-container {
                margin-top: 10px;
            }
        }
    </style>
    <title>Sicron - Task Management</title>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar__container">
            <div class="sidebar__user">
                <div class="sidebar__img">
                    <img src="assets/img/perfil.png" alt="Profile image">
                </div>
                <div class="sidebar__info">
                    <h3>Edson</h3>
                    <span>Edson@email.com</span>
                </div>
            </div>
            <div class="sidebar__content">
                <h3 class="sidebar__title">Menu</h3>
                <div class="sidebar__list">
                    <a href="#" class="sidebar__link active-link">
                        <i class="ri-pie-chart-2-fill"></i>
                        <span>Home</span>
                    </a>
                    <a href="#" class="sidebar__link">
                        <i class="ri-wallet-3-fill"></i>
                        <span>Salas</span>
                    </a>
                    <a href="#" class="sidebar__link">
                        <i class="ri-calendar-fill"></i>
                        <span>Caléndario</span>
                    </a>
                    <a href="#" class="sidebar__link">
                        <i class="ri-arrow-up-down-line"></i>
                        <span>Membros</span>
                    </a>
                    <a href="#" class="sidebar__link">
                        <i class="ri-bar-chart-box-fill"></i>
                        <span>Chat</span>
                    </a>
                </div>
            </div>
            <div class="sidebar__actions">
                <button>
                    <i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button">
                        <span>Tema</span>
                    </i>
                </button>
                <button class="sidebar__link">
                    <i class="ri-logout-box-r-fill"></i>
                    <span>Sair</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <div class="header">
            <button>Criar tarefa</button>
            <input type="text" placeholder="Pesquisar...">
            <div class="icons">
                <i class="fas fa-user"></i>
                <i class="fas fa-filter"></i>
                <i class="fas fa-sort"></i>
                <i class="fas fa-eye-slash"></i>
                <i class="fas fa-layer-group"></i>
            </div>
        </div>
        <div class="table-container">
            <div class="table-header">
                <h2>Este mês</h2>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox"></th>
                        <th>Tarefa</th>
                        <th>Responsável</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Prioridade</th>
                        <th>Notas</th>
                        <th>Orçamento</th>
                        <th>Arquivos</th>
                        <th>Cronograma</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>Projeto 1</td>
                        <td><i class="fas fa-user-circle"></i></td>
                        <td class="status"><span class="status-em-andamento">Em andamento</span></td>
                        <td>26 set</td>
                        <td><span class="priority-baixa">Baixa</span></td>
                        <td>[Exemplo] Element...</td>
                        <td>R$ 100</td>
                        <td><i class="fas fa-image"></i></td>
                        <td>26 - 27 set</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>[Exemplo] R...</td>
                        <td><i class="fas fa-user-circle"></i></td>
                        <td class="status"><span class="status-feito">Feito</span></td>
                        <td>27 set</td>
                        <td><span class="priority-alta">Alta</span></td>
                        <td></td>
                        <td>R$ 1.000</td>
                        <td></td>
                        <td>28 - 29 set</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>[Exemplo] R...</td>
                        <td><i class="fas fa-user-circle"></i></td>
                        <td class="status"><span class="status-parado">Parado</span></td>
                        <td>28 set</td>
                        <td><span class="priority-media">Média</span></td>
                        <td><i class="fas fa-font"></i></td>
                        <td>R$ 500</td>
                        <td></td>
                        <td>30 set - 1 out</td>
                    </tr>
                </tbody>
            </table>
            <div class="add-task">+ Adicionar tarefa</div>
            <div class="total">
                <div>
                    <span style="background-color: #ff9800; padding: 5px 10px; border-radius: 5px;">&nbsp;</span>
                    <span style="background-color: #4caf50; padding: 5px 10px; border-radius: 5px;">&nbsp;</span>
                    <span style="background-color: #f44336; padding: 5px 10px; border-radius: 5px;">&nbsp;</span>
                </div>
                <div>
                    <span>26 - 28 set</span>
                    <span style="background-color: #673ab7; padding: 5px 10px; border-radius: 5px;">&nbsp;</span>
                </div>
                <div>
                    <span>R$ 1.600 Total</span>
                </div>
                <div>
                    <span>1 Arquivos</span>
                </div>
            </div>
        </div>
        <div class="add-group">+ Adicionar novo grupo</div>
    </div>
</body>
</html>