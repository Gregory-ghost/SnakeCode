<?php

class Logic {
    private $struct;

    public function __construct($struct) {
        $this->struct = $struct;
    }

	//
	// Удав. Логика
	//
	
	// Получить удава
    public function getSnake( $options = null ) {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ($snake->id === $options) {
                    return $snake;
                }
            }
        }
        return false;
    }

	// Создать удава
    public function createSnake($options = null) {
        if ($options ) {
			$this->struct->snakes[] = new Snake($options->snake);
			return true;
        }
        return false;
    }

	// Уничтожить удава
    public function destroySnake($options = null) {
        if ( $options ) {
            $snakes = $this->struct->snake;
               foreach ($snakes as $key => $snake) {
                    if ( $snake->id === $options ) {
                        unset($this->struct->snakes[$key]);
                        return true;
                    }
                }
        }
        return false;
    }

	// Подвинуть удава на 1 клетку
    public function moveSnake($options = null) {
        if ( $options ) {
            $snake = $this->getSnake($options);
            if ( $snake ) {
				// Координаты головы удава
				$x = $snake->body[0]->x;
				$y = $snake->body[0]->y;
				
				// Направление
                switch ( $snake->direction ) {
                    case 'up':
                        $y--;
                        break;
                    case 'down':
                        $y++;
                        break;
                    case 'left':
                        $x--;
                        break;
                    case 'right':
                        $x++;
                        break;
                }
				
				$cOptions = (object) array(
					'id' => $options,
					'x' => $x,
					'y' => $y,
				);
				$isMove = isCanMove($cOptions);
				if ( $isMove ) {
						$isUpdatePosition = setPositionSnake( $cOptions );
						if ( $isUpdatePosition ) {
							// Координаты обновлены
							return true;
						}
				}
            }
        }
        return false;
    }
	
	// Установить координаты удава
	private function setPositionSnake( $options = null ) {
        if ( $options ) {
			$snakes = $this->struct->snakes;
			if ( $options->x and $options->y ) {
				$x = $options->x;
				$y = $options->y;
				foreach( $snakes as $key => $snake ) {
					if ( $snake->id == $options->id ) {
						// Устанавливаем координаты тела
						$body = $snake->body;
						$lastCoord = (object) array(
							'x' => $x,
							'y' => $y,
						);
						$tBody = [];
						foreach( $body as $bcoord ) {
							// Сдвигаем все тело удава
							$tBody[] = $lastCoord;
							$lastCoord->x = $bcoord->x;
							$lastCoord->y = $bcoord->y;
						}
						
						// Обновляем тело удава
						$this->struct->snakes[$key]->body = $tBody;
						return true;
					}
				}
			}		
		}
		return false;
	}
	
	// Может ли удав двигаться дальше
	private function isCanMove($options = null) {
		if ( $options ) {
			$mapSize = getMapSize(); // Получаем размер карты
			$x = $options->x;
			$y = $options->y;
			$id = $options->id;
			
			// Проверяем выход за границы карты
			$isCrashed = isCrashedInMap($options);
			// Проверяем врезался ли в другого удава
			$isCrashed = isCrashedInSnake($options);
			
			if ( $isCrashed ) {
				// Столкнулись, поэтому не можем идти дальше
				return false;
				
			} else {
				// Ни с чем не столкнулись, можем идти дальше
				return true;
			}
		}
		return false;
	}
	
	// Проверяем врезался ли в другого удава
	private function isCrashedInSnake( $options = null ) {
		if ( $options ) {
			$snakes = $this->struct->snakes;
			foreach ($snakes as $snake) {
                if ( $snake->id != $options->id ) {
					// Это другой удав
					$body = $snake->body;
					foreach ($body as $bcoord) {
						// Тело удава
						if ( $bcoord->x == $x and $bcoord->y == $y ) {
							// Врезались в удава
							return true;
						}
					}
                    
                }
            }
		}
		return false;
	}

    // Проверяем границы карты
    private function isCrashedInMap( $options = null ) {
        if ( $options ) {
            if ( $x > $mapSize or $x < 0 ) {
				return true;
			}
			if ( $y > $mapSize or $y < 0 ) {
				return true;
			}
        }
        return false;
    }
	
	// Получает размер карты
	private function getMapSize( $options = null ) {
		$maps = $this->struct->map;
		$mapSize = count($maps);
		
		return $mapSize;
	}
	
	// Изменить направление удава
    public function changeDirection($options = null)
    {
        if ($options) {
            $snake = $this->getSnake($options);
            if ( $snake && $options->direction ) {
                $snake->direction = $options->direction;
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
