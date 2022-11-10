<?php

class ReceptionGateway{
    private PDO $conn;

    public function __constructor(Database $database){
        $this->conn = $database->getConnection();
    }

//Room features

    public function getAllRooms(): array{
        $sql = "SELECT * 
                FROM room";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return $data;
    }


    public function getRoomByID(int $id): array | false{
        $sql = "SELECT * 
                FROM room 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function getAllAvailableRooms(): array | false{
        //TODO
    }


    public function createNewRoom(array $data): string{
        $sql = "INSERT INTO room (beds, type, cost_per_day) 
                VALUES (:beds, :type, :cost_per_day)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":beds", $data["beds"], PDO::PARAM_INT);
        $stmt->bindValue(":type", $data["type"], PDO::PARAM_STR);
        $stmt->bindValue(":cost_per_day", $data["cost_per_day"], PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->conn->lastInsertId();
    }

    
    public function deleteRoom(int $id): int{
        $sql = "DELETE FROM room WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->rowCount();
    }


    public function updateRoomInfo(int $id, array $data): int{
        //TODO
    }


//Reservation features

    public function getReservasionInfo(int $id): array | false{
        $sql = "SELECT * 
                FROM room_reservation 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }     


    public function addReservation(array $data): string{
        $sql = "INSERT INTO room_reservation (customer_id, checkin, checkout, room_id, billed, breakfast, lunch, dinner) 
                VALUES (:customer_id, :checkin, :checkout, :room_id, :billed, :breakfast, :lunch, :dinner)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":customer_id", $data["customer_id"], PDO::PARAM_INT);
        //TODO: bind checkin value
        //TODO: bind checkout value
        $stmt->bindValue(":room_id", $data["room_id"], PDO::PARAM_INT);
        $stmt->bindValue(":billed", $data["billed"], PDO::PARAM_BOOL);
        $stmt->bindValue(":breakfast". $data["breakfast"], PDO::PARAM_BOOL);
        $stmt->bindValue(":lunch", $data["lunch"], PDO::PARAM_BOOL);
        $stmt->bindValue(":dinner", $data["dinner"], PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }


    public function deleteReservation(int $id): int{
        $sql = "DELETE FROM room_reservation WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


    public function updateReservationInfo(int $id, array $data): int{
        //TODO
    }

//Customer features

    public function getAllCustomers(): array{
        $sql = "SELECT * FROM customer";

        $stmt - $this->conn->prepare($sql);

        $stmt->execute();

        $data = [];

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return $data;
    }


    public function getCustomerByID(int $id): array | false{
        $sql = "SELECT * FROM customer WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id);
        
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }


    public function newCustomer(array $data): string{
        $sql = "INSERT INTO customer (fisrtname, latname, phone) VALUES (:firstname, :lastname, :phone)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":firstname", $data["firstname"], PDO::PARAM_STR);
        $stmt->bindValue(":lastname", $data["lastname"], PDO::PARAM_STR);
        $stmt->bindValue(":phone", $data["phone"], PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }


    public function deleteCustomer(int $id): int{
        $sql = "DELETE FROM customer WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id);

        $stmt->execute();

        return $stmt->rowCount();
    }


    public function updateCustomerInfo(int $id, array $data): int{
        //TODO
    }
}