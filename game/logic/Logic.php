<?php

class Logic {
    private $struct;

    public function __construct($struct)
    {
        $this->struct = $struct;
    }

	//
	// Удав. Логика
	//
	
	// Получить удава
    public function getSnake( $options = null )
    {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ($options->id && $snake->id === $options->id) {
                    return $snake;
                }
            }
        }
        return false;
    }

	// Создать удава
    public function createSnake($options = null)
    {
        if ($options ) {
			$this->struct->snakes[] = new Snake($options->snake);
			return true;
        }
        return false;
    }

	// Уничтожить удава
    public function destroySnake($options = null)
    {
        if ( $options ) {
            $snakes = $this->struct->snake;
               foreach ($snakes as $key => $snake) {
                    if ( $options->id && $snake->id === $options->id ) {
                        unset($this->struct->snakes[$key]);
                        return true;
                    }
                }
        }
        return false;
    }

	// Подвинуть удава на 1 клетку
	// TODO :: check for limit of area
    public function moveSnake($options = null)
    {
        if ( $options ) {
            $snake = $this->getSnake($options->id);
            if ($snake) {
                switch ( $snake->direction ) {
                    case 'up':
                        $snake->y = $snake->y - 1;
                        break;
                    case 'down':
                        $snake->y = $snake->y + 1;
                        break;
                    case 'left':
                        $snake->x = $snake->x - 1;
                        break;
                    case 'right':
                        $snake->x = $snake->x + 1;
                        break;
                    default:
                        break;
                }
                return $this->checkForArea($snake);
            }
        }
        return false;
    }

    // Доступно ли передвижение для удава, границы карты
    public function checkForArea($snake = null) {
        if ( $snake ) {
            $map = count($this->struct->map)

            foreach
        }

        return false;
    }

	// Изменить направление удава
    public function changeDirection($options = null)
    {
        if ($options) {
            $snake = $this->getSnake($options->id);
            if ( $snake && $options-> $direction ) {
                $snake->direction = $options->$direction;
                return true;
            }
        }
        return false;
    }
	
	//
	// Еда. Логика
	//
	
	// Добавить новую еду на карту
    public function addFood($food)
    {
        if ($food) {
            $this->struct->foods[] = new Food($food);
        }
    }
	// Получить еду
    public function getFood($id)
    {
        if ($id) {
            $foods = $this->struct->foods;
            foreach ($foods as $food) {
                if ($food->id === $id) {
                    return $food;
                }
            }
        }
    }
	// Съесть еду
    public function eatFood($id)
    {
		$foods = $this->struct->foods;
           foreach ($foods as $key => $food) {
                if ($food->id === $id) {
                    unset($this->struct->foods[$key]);
					return true;
                }
            }
    }
}
