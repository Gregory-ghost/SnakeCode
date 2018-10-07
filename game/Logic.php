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
    private function getSnake($id)
    {
        if ($id) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ($snake->id === $id) {
                    return $snake;
                }
            }
        }
    }
	// Создать удава
    private function createSnake($snake)
    {
        if ($snake) {
			$this->struct->snakes[] = new Snake($snake);
			return true;
        }
    }
	// Уничтожить удава
    private function DestroySnake($id)
    {
		$snakes = $this->struct->snake;
           foreach ($snakes as $key => $snake) {
                if ($snake->id === $id) {
                    unset($this->struct->snakes[$key]);
					return true;
                }
            }
    }
	// Подвинуть удава на 1 клетку
    public function MoveSnake($id)
    {
        $snake = $this->getSnake($id);
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
            return true;
        }
    }
	// Изменить направление удава
    public function ChangeDirection($id, $direction)
    {
        $snake = $this->getSnake($id);
        if ($snake && $direction) {
            $snake->direction = $direction;
            return true;
        }
    }
	
	//
	// Еда. Логика
	//
	
	// Добавить новую еду на карту
    private function AddFood($food)
    {
        if ($food) {
            $this->struct->foods[] = new Food($food);
        }
    }
	// Получить еду
    private function getFood($id)
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
    private function eatFood($id)
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
